import { AudioPlayerService, AudioPlayerState, AudioPlayerStatus } from './AudioPlayerService'
import { computed, makeObservable, observable, runInAction } from 'mobx'
import createDebug from 'debug'

interface QueueEntry {
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

  constructor(private readonly audioPlayerService: AudioPlayerService) {
    makeObservable(this, {
      offset: observable,
      queue: observable,
      currentEntry: computed,
      audioPlayerState: computed,
    })

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
      await this.playCurrentEntry()
    }
  }

  public async playPrevious(): Promise<void> {
    this.offset = Math.max(this.offset - 1, 0)
    await this.playCurrentEntry()
  }

  public async playPause(): Promise<void> {
    if (this.audioPlayerService.state.status === AudioPlayerStatus.Paused) {
      await this.audioPlayerService.resume()
    } else {
      await this.audioPlayerService.pause()
    }
  }

  public async seek(time: number): Promise<void> {
    this.audioPlayerService.seek(time)
  }

  private async playCurrentEntry(): Promise<void> {
    const entry = this.queue[this.offset]
    if (!entry) return
    await this.audioPlayerService.play(entry.src)
  }
}

AudioPlayerQueueService.$inject = [AudioPlayerService.name]
