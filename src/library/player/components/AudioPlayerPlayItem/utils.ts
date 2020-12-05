import { QueueEntry } from '../../services/AudioPlayerQueueService'

export function trackToQueueEntry(track: Track): QueueEntry {
  return {
    trackId: track.id,
    artist: track.track_artist,
    title: track.track_title,
    length: track.length / 1000,
    rating: track.track_rating,
    src: track.format === 'mp3' ? `/file/${track.file_id}` : `/preview/${track.id}`,
  }
}
