from contextlib import asynccontextmanager
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import asyncio
import json
import logging
import redis.asyncio as aioredis

from routers import comments, ws
from database import db_execute

logger = logging.getLogger("uvicorn.error")

async def redis_subscriber():
    logger.info("redis_subscriber: starting")
    redis = aioredis.from_url("redis://127.0.0.1:6379", decode_responses=True)
    pubsub = redis.pubsub()

    await pubsub.subscribe(
        "laravel-database-new_post",
        "laravel-database-update_post",
        "laravel-database-delete_post",
        "laravel-database-user.renamed",
    )
    logger.info("redis_subscriber: subscribed to laravel-database-new_post, laravel-database-update_post, laravel-database-delete_post, laravel-database-user.renamed")

    try:
        while True:
            message = await pubsub.get_message(ignore_subscribe_messages=True, timeout=1.0)

            if message is None:
                await asyncio.sleep(0.1)
                continue

            logger.info(f"redis_subscriber raw message: {message}")

            channel = message["channel"]
            data = json.loads(message["data"])

            logger.info(f"redis_subscriber: message from {channel}: {data}")

            if channel == "laravel-database-new_post":
                await ws.manager.broadcast({
                    "type": "new_post",
                    "post": data
                })
                logger.info("redis_subscriber: broadcast new_post sent")

            elif channel == "laravel-database-update_post":
                await ws.manager.broadcast({
                     "type": "update_post",
                     "post": data
                })
                logger.info("redis_subscriber: broadcast update_post sent")

            elif channel == "laravel-database-delete_post":
                await ws.manager.broadcast({
                    "type": "delete_post",
                    "post_id": data["id"]
                })
                logger.info("redis_subscriber: broadcast delete_post sent")

            elif channel == "laravel-database-user.renamed":
                await db_execute(
                    "UPDATE comments SET author_name=%s WHERE author_id=%s",
                    data["new_name"], data["id"]
                )
                await ws.manager.broadcast({
                    "type": "user_renamed",
                    "user_id": data["id"],
                    "new_name": data["new_name"]
                })
                logger.info("redis_subscriber: broadcast user_renamed sent")

            logger.info("redis_subscriber: broadcast delete_post sent")

    except asyncio.CancelledError:
        logger.info("redis_subscriber: cancelled")
        raise
    except Exception:
        logger.exception("redis_subscriber: crashed")
        raise
    finally:
        await pubsub.close()
        await redis.close()

@asynccontextmanager
async def lifespan(app: FastAPI):
    logger.info("lifespan: startup")
    task = asyncio.create_task(redis_subscriber())
    yield
    logger.info("lifespan: shutdown")
    task.cancel()

app = FastAPI(title="Boardy API", version="0.5.0", lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["https://kilqa.ai-info.ru"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(comments.router)
app.include_router(ws.router)
