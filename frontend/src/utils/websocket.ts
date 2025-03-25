import type { Weather } from "@/types/ApiTypes";

interface WebSocketMessage {
  event: string;
  data: { userId: number; weather: Weather | null };
}

type WeatherHandler = (data: {
  userId: number;
  weather: Weather | null;
}) => void;

class WebSocketClient {
  private ws: WebSocket | null = null;
  private handlers: Map<string, WeatherHandler[]>;

  constructor() {
    this.handlers = new Map();
    this.connect();
  }

  private connect(): void {
    const wsUrl = `ws://${import.meta.env.VITE_PUSHER_HOST}:${
      import.meta.env.VITE_PUSHER_PORT
    }/app/${import.meta.env.VITE_PUSHER_APP_KEY}`;
    console.log("Connecting to WebSocket:", wsUrl);
    this.ws = new WebSocket(wsUrl);

    this.ws.onopen = () => {
      console.log("WebSocket Connected");
      // Subscribe to the weather channel
      if (this.ws && this.ws.readyState === WebSocket.OPEN) {
        this.ws.send(
          JSON.stringify({
            event: "subscribe",
            channel: "weather",
          })
        );
      }
    };

    this.ws.onerror = (error: Event) => {
      console.error("WebSocket connection error:", error);
    };

    this.ws.onmessage = (event: MessageEvent) => {
      try {
        const data: WebSocketMessage = JSON.parse(event.data);
        if (data.event === "WeatherUpdated") {
          const handlers = this.handlers.get("weather") || [];
          handlers.forEach((handler) => handler(data.data));
        }
      } catch (error) {
        console.error("Failed to parse WebSocket message:", error);
      }
    };

    this.ws.onclose = () => {
      console.log("WebSocket connection closed. Reconnecting...");
      setTimeout(() => this.connect(), 1000);
    };
  }

  onWeatherUpdate(
    callback: (data: { userId: number; weather: Weather | null }) => void
  ): void {
    if (!this.handlers.has("weather")) {
      this.handlers.set("weather", []);
    }
    this.handlers.get("weather")?.push(callback);
  }
}

export default new WebSocketClient();
