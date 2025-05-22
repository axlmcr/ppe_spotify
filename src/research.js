const popup = document.getElementById('popup');
const popupResults = document.getElementById('popupResults');
const closePopupBtn = document.getElementById('closePopup');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
let player; // Global variable for the YouTube player

// Elements for the player controls
const playPauseBtn = document.getElementById('playPauseBtn');
const prevTrackBtn = document.getElementById('prevTrackBtn');
const nextTrackBtn = document.getElementById('nextTrackBtn');
const currentTimeDisplay = document.getElementById('currentTime');
const totalTimeDisplay = document.getElementById('totalTime');
const seekBar = document.getElementById('seekBar');
const volumeControl = document.getElementById('volumeControl');

// Track list and current track index
let trackList = [];
let currentTrackIndex = 0;
let isPlaying = false;

// Function to display the popup with results
function showPopup(resultsHTML) {
    popupResults.innerHTML = resultsHTML; // Insert results into the popup
    popup.style.display = 'flex';        // Show the popup
}

// Function to close the popup
function closePopup() {
    popup.style.display = 'none';        // Hide the popup
}

// Add an event listener to close the popup
closePopupBtn.addEventListener('click', closePopup);

// Function to search for music and display results in the popup
function searchMusic(query) {
    fetch(`http://localhost:3000/search?q=${encodeURIComponent(query)}`)
        .then(res => {
            if (!res.ok) {
                throw new Error('Error during the search.');
            }
            return res.json();
        })
        .then(data => {
            if (data.length === 0) {
                showPopup('<p>No results found.</p>');
                return;
            }

            // Update the track list
            trackList = data.map(item => ({
                id: item.id,
                title: item.title,
                channelTitle: item.channelTitle,
                length: item.length
            }));

            // Generate the HTML for the results
            const resultsHTML = data.map(item => `
                <div class="result-item" style="cursor: pointer;" onclick="loadTrack(${trackList.findIndex(track => track.id === item.id)})">
                    <img src="${item.thumbnail}" alt="${item.title}" />
                    <span><strong>${item.title}</strong></span>
                    <span>Artist: ${item.channelTitle}</span>
                    <span>Duration: ${item.length}</span>
                </div>
            `).join('');

            showPopup(resultsHTML);      // Display the results in the popup
        })
        .catch(err => {
            console.error('Backend error:', err);
            showError('An error occurred during the search.');
        });
}

// Function to display errors
function showError(message) {
    const errorContainer = document.getElementById('errorContainer');
    errorContainer.textContent = message;
    errorContainer.style.display = 'block';
    setTimeout(() => {
        errorContainer.style.display = 'none';
    }, 5000); // Hide error after 5 seconds
}

// Function to load and play a track
function loadTrack(index) {
    if (index < 0 || index >= trackList.length) return;
    currentTrackIndex = index;
    const track = trackList[index];
    loadAudio(track.id, track.title, track.channelTitle, track.length);
}

// Function to check if the player is ready
function isPlayerReady() {
    return player && typeof player.playVideo === 'function';
}

function loadAudio(videoId, title, channelTitle = "Unknown", length = "0:00") {
    const audioPlayerContainer = document.getElementById('audioPlayerContainer');
    if (audioPlayerContainer) {
        audioPlayerContainer.style.display = 'block'; // Show the audio container
    }

    if (player) {
        player.loadVideoById(videoId);
    } else {
        player = new YT.Player('player', {
            height: '0',                  // Hide the video
            width: '0',                   // Hide the video
            videoId: videoId,
            playerVars: {
                autoplay: 1,
                controls: 0,              // Disable video controls
                modestbranding: 1,        // Reduce YouTube branding
                rel: 0,                   // Disable suggested videos at the end
                showinfo: 0               // Hide video information
            },
            events: {
                onReady: () => {
                    console.log('Lecteur YouTube prêt');
                    player.playVideo();
                },
                onStateChange: onPlayerStateChange,
                onError: (event) => console.error('YouTube player error:', event)
            }
        });
    }

    // Update the current track information
    const trackTitleElement = document.getElementById('trackTitle');
    if (trackTitleElement) {
        trackTitleElement.textContent = title;
    } else {
        console.error('L\'élément trackTitle est introuvable.');
    }

    const trackArtistElement = document.getElementById('trackArtist');
    if (trackArtistElement) {
        trackArtistElement.textContent = channelTitle;
    } else {
        console.error('L\'élément trackArtist est introuvable.');
    }

    const trackLengthElement = document.getElementById('tracklength');
    if (trackLengthElement) {
        trackLengthElement.textContent = length;
    } else {
        console.error('L\'élément tracklength est introuvable.');
    }
}

playPauseBtn.addEventListener('click', () => {
    const icon = playPauseBtn.querySelector('img');
    if (isPlayerReady()) {
        if (isPlaying) {
            player.pauseVideo();
            icon.src = 'assets/icones/play-solid.svg'; // Icône de lecture
            icon.alt = 'Lecture';
        } else {
            player.playVideo();
            icon.src = 'assets/icones/pause-solid.svg'; // Icône de pause
            icon.alt = 'Pause';
        }
        isPlaying = !isPlaying;
    } else {
        console.error('Le lecteur YouTube n\'est pas prêt.');
    }
});

// Function to handle previous track
prevTrackBtn.addEventListener('click', () => {
    if (currentTrackIndex > 0) {
        loadTrack(currentTrackIndex - 1);
    }
});

// Function to handle next track
nextTrackBtn.addEventListener('click', () => {
    if (currentTrackIndex < trackList.length - 1) {
        loadTrack(currentTrackIndex + 1);
    }
});

// Function to update the time and seek bar
function updateTime() {
    if (isPlayerReady() && player.getDuration) {
        const currentTime = player.getCurrentTime();
        const duration = player.getDuration();
        currentTimeDisplay.textContent = formatTime(currentTime);
        totalTimeDisplay.textContent = formatTime(duration);
        seekBar.value = (currentTime / duration) * 100;
    }
}

// Seek functionality
seekBar.addEventListener('input', () => {
    if (isPlayerReady()) {
        const duration = player.getDuration();
        const seekTo = (seekBar.value / 100) * duration;
        player.seekTo(seekTo);
    }
});

// Volume control
volumeControl.addEventListener('input', () => {
    if (isPlayerReady()) {
        player.setVolume(volumeControl.value * 100);
    }
});

// Format time in mm:ss
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
}

// Update the time display every second
setInterval(() => {
    if (isPlayerReady() && isPlaying) {
        updateTime();
    }
}, 1000);

// Listener for input in the search field
searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();
    if (query.length >= 3) searchMusic(query);
});

// Listener for the search button click
searchBtn.addEventListener('click', () => {
    const query = searchInput.value.trim();
    if (query.length >= 3) searchMusic(query);
});

// Handle player state changes
function onPlayerStateChange(event) {
    const icon = playPauseBtn.querySelector('img');
    if (event.data === YT.PlayerState.PLAYING) {
        isPlaying = true;
        icon.src = 'assets/icones/pause-solid.svg'; // Icône de pause
    } else if (event.data === YT.PlayerState.PAUSED) {
        isPlaying = false;
        icon.src = 'assets/icones/play-solid.svg'; // Icône de lecture
    }
}