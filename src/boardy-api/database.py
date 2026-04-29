import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',     
    'password': 'pass',
    'db': 'boardy',              
    'charset': 'utf8mb4',      
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)
