import pika
import json
import requests

# Setup RabbitMQ connection
connection = pika.BlockingConnection(pika.ConnectionParameters(host='100.93.130.48'))
channel = connection.channel()

# Declare queues
request_queue = 'dmz_requests'
response_queue = 'dmz_responses'

channel.queue_declare(queue=request_queue)
channel.queue_declare(queue=response_queue)

print("[DMZ] Waiting for messages...")

# RabbitMQ request handler
def on_request(ch, method, properties, body):
    print("[DMZ] Received request")
    request = json.loads(body)

    if request.get("type") != "get_recommendations":
        print("[DMZ] Unsupported request type")
        ch.basic_ack(delivery_tag=method.delivery_tag)
        return

    genre = request.get("genre", "")
    year_range = request.get("year", "")  # e.g., "2000-2020"
    rating = request.get("rating", "")

    # Default fallback response
    result = {
        "returnCode": 1,
        "message": "Failed to fetch data",
        "recommendations": []
    }

    try:
        # Parse year range
        year_start, year_end = (year_range.split("-") + [""])[:2]

        url = "https://moviesdatabase.p.rapidapi.com/titles"

        querystring = {
            "genre": genre,
            "startYear": year_start.strip(),
            "endYear": year_end.strip(),
            "limit": "10",
            "minRating": rating
        }

        headers = {
            "x-rapidapi-host": "moviesdatabase.p.rapidapi.com",
            "x-rapidapi-key": "b982b66939msh3b305adfeb3a70ep1ae7f0jsn69e1de67d99e"
        }

        response = requests.get(url, headers=headers, params=querystring)
        data = response.json()

        movies = []
        if "results" in data:
            for movie in data["results"]:
                title = movie.get("titleText", {}).get("text", "Untitled")
                year = movie.get("releaseYear", {}).get("year", "N/A")
                movies.append(f"{title} ({year})")

        result = {
            "returnCode": 0,
            "recommendations": movies
        }

    except Exception as e:
        print("[DMZ] Error:", e)
        result["message"] = str(e)

    # Send back the result
    channel.basic_publish(
        exchange='',
        routing_key=response_queue,
        body=json.dumps(result)
    )

    print("[DMZ] Response sent to response queue")
    ch.basic_ack(delivery_tag=method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(queue=request_queue, on_message_callback=on_request)

try:
    channel.start_consuming()
except KeyboardInterrupt:
    print("Shutting down DMZ service.")
    channel.stop_consuming()
    connection.close()
