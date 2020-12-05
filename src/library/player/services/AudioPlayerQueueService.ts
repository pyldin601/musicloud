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

export type LoadMoreFn = (offset: number) => Promise<ReadonlyArray<QueueEntry>>

export class AudioPlayerQueueService {
  private debug = createDebug(AudioPlayerQueueService.name)

  private loadMore: LoadMoreFn | null = null

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

  public async play(
    queue: ReadonlyArray<QueueEntry>,
    offset = 0,
    loadMore: LoadMoreFn | null = null,
  ): Promise<void> {
    runInAction(() => {
      this.queue = [...queue]
      this.offset = offset
      this.loadMore = loadMore
    })
    await this.playCurrentEntry()
  }

  public async playNext(): Promise<void> {
    if (this.offset >= this.queue.length - 1) return

    this.offset += 1
    this.debug('playNext')

    await this.playCurrentEntry()

    if (this.isLastEntry && this.loadMore) {
      const loadedEntries = await this.loadMore(this.queue.length)

      if (loadedEntries.length === 0) {
        this.debug('loadMore: no more')
        this.loadMore = null
      } else {
        this.debug('loadMore', { loadedEntries })
        this.queue = this.queue.concat(loadedEntries)
      }
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

  public async seekPercent(percent: number): Promise<void> {
    this.debug('seekPercent', { percent })
    if (!this.currentEntry) return
    this.audioPlayerService.seek(this.currentEntry.length * (percent / 100))
  }

  private async playCurrentEntry(): Promise<void> {
    const entry = this.queue[this.offset]
    if (!entry) return
    this.debug('playCurrentEntry', { entry: toJS(entry) })
    await this.audioPlayerService.play(entry.src)
  }

  private get isLastEntry(): boolean {
    return this.offset === this.queue.length - 1
  }
}

AudioPlayerQueueService.$inject = [AudioPlayerService.name]
