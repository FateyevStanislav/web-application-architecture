from contextlib import asynccontextmanager
from fastapi import FastAPI
import asyncio
import json
import logging
import redis.asyncio as aioredis

from routers import comments, ws

logger = logging.getLogger("uvicorn.error")

async def redis_subscriber():
    logger.info("redis_subscriber: starting")
    redis = aioredis.from_url("redis://127.0.0.1:6379", decode_responses=True)
    pubsub = redis.pubsub()
    await pubsub.subscribe("new_post", "user.renamed")
    logger.info("redis_subscriber: subscribed to new_post, user.renamed")

    async for message in pubsub.listen():
        if message["type"] != "message":
            continue

        channel = message["channel"]
        data = json.loads(message["data"])

        logger.info(f"redis_subscriber: message from {channel}: {data}")

        if channel == "new_post":
            await ws.manager.broadcast({
                "type": "new_post",
                "post": data
            })
            logger.info("redis_subscriber: broadcast new_post sent")

        elif channel == "user.renamed":
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

@asynccontextmanager
async def lifespan(app: FastAPI):
    logger.info("lifespan: startup")
    task = asyncio.create_task(redis_subscriber())
    yield
    logger.info("lifespan: shutdown")
    task.cancel()

app = FastAPI(title="Boardy API", version="0.5.0", lifespan=lifespan)

app.include_router(comments.router)
app.include_router(ws.router)
