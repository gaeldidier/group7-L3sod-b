from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI()

# List of connected clients
clients = {}

@app.websocket("/ws/{username}")
async def websocket_endpoint(websocket: WebSocket, username: str):
    await websocket.accept()
    clients[username] = websocket
    try:
        while True:
            message = await websocket.receive_text()
            for user, client in clients.items():
                if user != username:
                    await client.send_text(f"{username}: {message}")
    except WebSocketDisconnect:
        clients.pop(username, None)
        await websocket.close()
