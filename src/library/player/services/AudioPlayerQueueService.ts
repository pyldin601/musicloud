import { AudioPlayerService, AudioPlayerState, AudioPlayerStatus } from './AudioPlayerService'
import { computed, makeObservable, observable, runInAction, toJS } from 'mobx'
import createDebug from 'debug'
import { queuedReaction } from '../../../utils/mobx'

export interface QueueEntry {
  trackId: string
  title: string
  artist: string
  length: number
  src: string
  rating: null | number
}

export class AudioPlayerQueueService {
  private debug = createDebug(AudioPlayerQueueService.name)

  public offset = 0
  public queue: Array<QueueEntry> = []

  public get currentEntry(): QueueEntry | null {
    return this.queue[this.offset] ?? null
  }

  public get audioPlayerState(): AudioPlayerState {
    return this.audioPlayerService.state
  }

  public get isPlaying(): boolean {
    return this.audioPlayerState.status === AudioPlayerStatus.Playing
  }

  public get isLoading(): boolean {
    return this.audioPlayerState.status === AudioPlayerStatus.Waiting
  }

  public get isPaused(): boolean {
    return this.audioPlayerState.status === AudioPlayerStatus.Paused
  }

  constructor(private readonly audioPlayerService: AudioPlayerService) {
    makeObservable(this, {
      offset: observable,
      queue: observable,
      currentEntry: computed,
      audioPlayerState: computed,
    })

    queuedReaction(
      () => this.audioPlayerState.status,
      async (status) => {
        if (status === AudioPlayerStatus.Ended) {
          this.debug('Playback ended: playNext')
          await this.playNext()
        }
      },
    )

    this.debug('Ready')
  }

  public async play(queue: ReadonlyArray<QueueEntry>, offset = 0): Promise<void> {
    runInAction(() => {
      this.queue = [...queue]
      this.offset = offset
    })
    await this.playCurrentEntry()
  }

  public async playNext(): Promise<void> {
    if (this.offset < this.queue.length - 1) {
      this.offset += 1
      this.debug('playNext')
      await this.playCurrentEntry()
    }
  }

  public async playPrevious(): Promise<void> {
    this.offset = Math.max(this.offset - 1, 0)
    this.debug('playPrevious')
    await this.playCurrentEntry()
  }

  public async playPause(): Promise<void> {
    if (this.audioPlayerService.state.status === AudioPlayerStatus.Paused) {
      this.debug('resume')
      await this.audioPlayerService.resume()
    } else {
      this.debug('pause')
      await this.audioPlayerService.pause()
    }
  }

  public async seek(time: number): Promise<void> {
    this.debug('seek', { time })
    this.audioPlayerService.seek(time)
  }

  private async playCurrentEntry(): Promise<void> {
    const entry = this.queue[this.offset]
    if (!entry) return
    this.debug('playCurrentEntry', { entry: toJS(entry) })
    await this.audioPlayerService.play(entry.src)
  }
}

AudioPlayerQueueService.$inject = [AudioPlayerService.name]
