import asyncio
import datetime
import websockets
import mysql

async def show_time(websocket, users):
    while websocket.open:
        await websocket.send("<p>" + await websocket.recv() + "</p>")
        await asyncio.sleep(1)

async def main():
    users = {}

    async with websockets.serve(show_time, host = "localhost", port = 5678):
        await asyncio.Future()  # run forever

if __name__ == "__main__":
    asyncio.run(main())