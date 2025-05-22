const express = require('express');
const cors = require('cors');
const Youtube = require('youtube-search-api');

const app = express();
app.use(cors()); // Allows cross-origin requests

// Route to search for videos on YouTube
app.get('/search', async (req, res) => {
    const query = req.query.q; // Retrieves the search query
    if (!query) return res.status(400).send('Missing query'); // Checks if a query is provided

    try {
        // Searches for videos on YouTube
        const results = await Youtube.GetListByKeyword(`${query} music`, false, 15);

        const keywords = [
            "music", "audio", "official", "lyrics", "track", "album",
            "clip", "instrumental", "remix", "live", "cover",
            "officiel", "visualizer", "paroles", "musique", "vidéo", "clip officiel",
            "musique officielle", "vidéo officielle", "prod",
            "feat", "feat.", "ft", "ft."
        ];

        // Filters results to include only relevant videos
        const filtered = results.items.filter(item => {
            if (!item || !item.title) return false;
            const title = item.title.toLowerCase();
            return keywords.some(kw => title.includes(kw));
        });

        // Formats results to include only necessary information
        const formattedResults = filtered.map(item => ({
            id: item.id,
            title: item.title,
            thumbnail: item.thumbnail?.thumbnails?.[0]?.url || "default-thumbnail.jpg", // Default thumbnail
            length: item.length?.simpleText || "N/A",                                  // Duration
            channelTitle: item.channelTitle || "Unknown"                              // Channel name
        }));

        if (formattedResults.length === 0) {
            return res.status(404).json({ message: 'No matching results found.' });
        }

        res.json(formattedResults); // Sends results to the frontend
    } catch (err) {
        console.error('YouTube search API error:', err);
        res.status(500).send('API error'); // Error handling
    }
});

// Test route to check if the server is running
app.get('/', (req, res) => {
    res.send('YouTube Music Search API is running!');
});

// Starts the server on port 3000
app.listen(3000, () => {
    console.log('Backend server running at http://localhost:3000');
});