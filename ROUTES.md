# MusicLoud API Routes Documentation

## Overview

MusicLoud is a PHP-based music streaming application with a custom routing system. The API provides comprehensive functionality for managing tracks, playlists, user accounts, and music metadata.

## Routing System

The application uses two main routing patterns:

1. **Fixed Routes**: Follow the pattern `/api/{category}/{action}` → `Do{Action}` class
2. **Dynamic Routes**: Defined with parameters using `when()` and `whenRegExp()` functions

## Authentication

Most API endpoints require authentication. Users must be logged in to access protected routes.

---

## Authentication Routes

### User Login

- **Endpoint**: `POST /api/login`
- **Handler**: `DoLogin::doPost`
- **Description**: User authentication and login

### User Logout

- **Endpoint**: `POST /api/logout`
- **Handler**: `DoLogout::doPost`
- **Description**: User logout

### Get Current User

- **Endpoint**: `GET /api/self`
- **Handler**: `DoSelf::doGet`
- **Description**: Get current user profile information

---

## User Management

### User Registration

- **Endpoint**: `POST /api/user/signup`
- **Handler**: `DoSignUp::doPost`
- **Description**: Create new user account

### Reset Password

- **Endpoint**: `POST /api/user/resetpassword`
- **Handler**: `DoResetPassword::doPost`
- **Description**: Request password reset

### Change Password

- **Endpoint**: `POST /api/self/changepassword`
- **Handler**: `DoChangePassword::doPost`
- **Description**: Change authenticated user's password

---

## Track Management

### Create Track

- **Endpoint**: `POST /api/track/create`
- **Handler**: `DoCreate::doPost`
- **Description**: Create new track record

### Upload Track

- **Endpoint**: `POST /api/track/upload`
- **Handler**: `DoUpload::doPost`
- **Description**: Upload audio file for track

### Edit Track

- **Endpoint**: `POST /api/track/edit`
- **Handler**: `DoEdit::doPost`
- **Description**: Edit track metadata

### Delete Track

- **Endpoint**: `POST /api/track/delete`
- **Handler**: `DoDelete::doPost`
- **Description**: Delete specific track

### Delete Tracks by Artist

- **Endpoint**: `POST /api/track/deletebyartist`
- **Handler**: `DoDeleteByArtist::doPost`
- **Description**: Delete all tracks by specific artist

### Create Track from Video

- **Endpoint**: `POST /api/track/createfromvideo`
- **Handler**: `DoCreateFromVideo::doPost`
- **Description**: Create track from video source

### Find Album Artwork

- **Endpoint**: `POST /api/track/findalbumartwork`
- **Handler**: `DoFindAlbumArtwork::doPost`
- **Description**: Automatically find and set album artwork

### Upload Artwork

- **Endpoint**: `POST /api/track/artwork`
- **Handler**: `DoArtwork::doPost`
- **Description**: Upload custom track artwork

---

## Playlist Management

### Create Playlist

- **Endpoint**: `POST /api/playlist/create`
- **Handler**: `DoCreate::doPost`
- **Description**: Create new playlist

### Get Playlist

- **Endpoint**: `GET /api/playlist/get`
- **Handler**: `DoGet::doGet`
- **Description**: Get playlist details and tracks

### Delete Playlist

- **Endpoint**: `POST /api/playlist/delete`
- **Handler**: `DoDelete::doPost`
- **Description**: Delete playlist

### Add Tracks to Playlist

- **Endpoint**: `POST /api/playlist/addtracks`
- **Handler**: `DoAddTracks::doPost`
- **Description**: Add tracks to existing playlist

### Remove Tracks from Playlist

- **Endpoint**: `POST /api/playlist/removetracks`
- **Handler**: `DoRemoveTracks::doPost`
- **Description**: Remove tracks from playlist

### Change Track Order

- **Endpoint**: `POST /api/playlist/changeorder`
- **Handler**: `DoChangeOrder::doPost`
- **Description**: Reorder tracks in playlist

---

## Catalog & Discovery

### Get Tracks

- **Endpoint**: `GET /api/catalog/tracks`
- **Handler**: `DoTracks::doGet`
- **Description**: Get tracks catalog with filtering and pagination

### Get Albums

- **Endpoint**: `GET /api/catalog/albums`
- **Handler**: `DoAlbums::doGet`
- **Description**: Get albums catalog

### Get Artists

- **Endpoint**: `GET /api/catalog/artists`
- **Handler**: `DoArtists::doGet`
- **Description**: Get artists catalog

### Get Genres

- **Endpoint**: `GET /api/catalog/genres`
- **Handler**: `DoGenres::doGet`
- **Description**: Get genres catalog

### Get Playlists

- **Endpoint**: `GET /api/catalog/playlists`
- **Handler**: `DoPlaylists::doGet`
- **Description**: Get public playlists catalog

### Get Compilations

- **Endpoint**: `GET /api/catalog/compilations`
- **Handler**: `DoCompilations::doGet`
- **Description**: Get compilations catalog

### Get Playlist Tracks

- **Endpoint**: `GET /api/catalog/playlisttracks`
- **Handler**: `DoPlaylistTracks::doGet`
- **Description**: Get tracks in specific playlist

---

## Statistics & Analytics

### Track Played

- **Endpoint**: `POST /api/stats/played`
- **Handler**: `DoPlayed::doPost`
- **Description**: Record track play statistics

### Track Skipped

- **Endpoint**: `POST /api/stats/skipped`
- **Handler**: `DoSkipped::doPost`
- **Description**: Record track skip statistics

### Rate Track

- **Endpoint**: `POST /api/stats/rate`
- **Handler**: `DoRate::doPost`
- **Description**: Rate a track

### Remove Rating

- **Endpoint**: `POST /api/stats/unrate`
- **Handler**: `DoUnrate::doPost`
- **Description**: Remove track rating

---

## Metadata & Headers

### Get Artist Header

- **Endpoint**: `GET /api/headers/artist`
- **Handler**: `DoArtist::doGet`
- **Description**: Get artist header information

### Get Genre Header

- **Endpoint**: `GET /api/headers/genre`
- **Handler**: `DoGenre::doGet`
- **Description**: Get genre header information

---

## Scrobbling Integration

### Now Playing

- **Endpoint**: `POST /api/scrobbler/nowplaying`
- **Handler**: `DoNowPlaying::doPost`
- **Description**: Report now playing track to Last.fm

### Scrobble Track

- **Endpoint**: `POST /api/scrobbler/scrobble`
- **Handler**: `DoScrobble::doPost`
- **Description**: Scrobble track to Last.fm

### Last.fm Callback

- **Endpoint**: `GET /lastfm-callback`
- **Handler**: `DoCallback::doGet`
- **Description**: Last.fm OAuth authentication callback

---

## Resource Management

### Get All Track Resources

- **Endpoint**: `GET /api/resources/tracks/all`
- **Handler**: `DoAll::doGet`
- **Description**: Get all track resources for sync

### Get Track Updates

- **Endpoint**: `GET /api/resources/tracks/update`
- **Handler**: `DoUpdate::doGet`
- **Description**: Get track updates since last sync

### Get Track by ID

- **Endpoint**: `GET /api/resources/track/:id`
- **Handler**: `DoTrack::doGet`
- **Description**: Get specific track resource by ID

---

## Content Delivery

### Get Track Preview

- **Endpoint**: `GET /preview/:id`
- **Handler**: `DoGetPreview::doGet`
- **Description**: Get track preview/audio file

### Get Track File

- **Endpoint**: `GET /file/:id`
- **Handler**: `DoGetFile::doGet`
- **Description**: Get full track file

### Get Waveform Data

- **Endpoint**: `GET /peaks/:id`
- **Handler**: `DoWavePeaks::doGet`
- **Description**: Get waveform peak data for visualization

---

## Library & Browsing

### Library Browse

- **Endpoint**: `GET /library/*`
- **Handler**: `DoLibrary::doGet`
- **Description**: Browse music library by folders

---

## Integrations

### Alexa Integration

- **Endpoint**: `GET /command-for-alexa/:id`
- **Handler**: `DoGetCommandForAlexa::doGet`
- **Description**: Generate Alexa voice commands

---

## Web Routes

### Main Application

- **Endpoint**: `GET /`
- **Handler**: `DoIndex::doGet`
- **Description**: Main application interface

### Registration Page

- **Endpoint**: `GET /registration`
- **Handler**: `DoRegistration::doGet`
- **Description**: User registration page

---

## Technical Details

### Response Format

- All API endpoints return JSON responses
- Success responses include data payload
- Error responses include error message and status code

### Authentication

- Most endpoints require valid user session
- Authentication handled via session middleware
- Unauthorized requests return 401 status

### File Uploads

- Track uploads support multiple audio formats
- File size limits configurable
- Artwork uploads support common image formats

### Rate Limiting

- Consider implementing rate limiting for public endpoints
- Statistics endpoints may need protection against spam

---

## Error Handling

Common HTTP status codes:

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

Error responses follow the format:

```json
{
  "error": "Error message",
  "status": 400
}
```

---

_This documentation covers all available API routes in the MusicLoud application as of the current version._
