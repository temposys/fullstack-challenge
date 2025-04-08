export type Weather = {
    latitude: number;
    longitude: number;
    countryCode: string | null;
    city: string;
    condition: string;
    description: string;
    icon: string;
    temperature: number;
    feelsLike: number;
    pressure: number;
    humidity: number;
    windSpeed: number;
    windAngle: number;
    windDirection: string;
    cloudiness: number;
    visibility: number;
    timezone: number;
    sunrise: string;
    sunset: string;
    calculatedAt: string;
};

export type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    latitude: string;
    longitude: string;
    created_at: string;
    updated_at: string;
    weather: Weather | null;
};
