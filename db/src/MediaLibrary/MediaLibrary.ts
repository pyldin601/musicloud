import { Track } from '../Track'

export interface MediaLibrary {
  getTracksByArtist(artist: string): Promise<ReadonlyArray<Track>>
  getTracksByArtistAndAlbum(artist: string, album: string): Promise<ReadonlyArray<Track>>
  getTracks(offset: number, total: number): Promise<ReadonlyArray<Track>>
}
