import { action, makeObservable, observable, runInAction } from 'mobx'
import createDebug from 'debug'

export enum AudioPlayerStatus {
  Stopped = 'Stopped',
  Waiting = 'Waiting',
  Playing = 'Playing',
  Paused = 'Paused',
  Ended = 'Ended',
}

interface AudioPlayerStoppedState {
  status: AudioPlayerStatus.Stopped | AudioPlayerStatus.Ended
}

interface AudioPlayerPlayingState {
  status: AudioPlayerStatus.Waiting | AudioPlayerStatus.Playing | AudioPlayerStatus.Paused
  src: string
  currentTime: number
  bufferedTime: number
}

export type AudioPlayerState = AudioPlayerStoppedState | AudioPlayerPlayingState

export class AudioPlayerService {
  private debug = createDebug(AudioPlayerService.name)

  public state: AudioPlayerState = {
    status: AudioPlayerStatus.Stopped,
  }

  private audio: HTMLAudioElement

  constructor() {
    this.audio = new Audio()

    this.audio.addEventListener('waiting', () => {
      this.setState({
        status: AudioPlayerStatus.Waiting,
        src: this.audio.src,
        currentTime: this.audio.currentTime,
        bufferedTime: this.bufferedTime,
      })
    })

    this.audio.addEventListener('timeupdate', () => {
      if (this.audio.paused) return

      this.setState({
        status: AudioPlayerStatus.Playing,
        src: this.audio.src,
        currentTime: this.audio.currentTime,
        bufferedTime: this.bufferedTime,
      })
    })

    this.audio.addEventListener('ended', () => {
      this.audio.removeAttribute('src')

      this.setState({
        status: AudioPlayerStatus.Ended,
      })
    })

    makeObservable(this, {
      state: observable,
      setState: action,
    })

    this.debug('Ready')
  }

  private get bufferedTime(): number {
    return this.audio.buffered.length > 0 ? this.audio.buffered.end(0) : 0
  }

  public async play(src: string): Promise<void> {
    this.audio.setAttribute('src', src)
    await this.audio.play()
  }

  public stop(): void {
    this.audio.pause()
    this.audio.removeAttribute('src')

    runInAction(() => {
      this.setState({
        status: AudioPlayerStatus.Stopped,
      })
    })
  }

  public pause(): void {
    this.audio.pause()
    this.setState({
      status: AudioPlayerStatus.Paused,
      src: this.audio.src,
      currentTime: this.audio.currentTime,
      bufferedTime: this.bufferedTime,
    })
  }

  public async resume(): Promise<void> {
    await this.audio.play()
  }

  public seek(time: number): void {
    this.audio.currentTime = time
  }

  public setState(state: AudioPlayerState): void {
    this.state = state
  }
}

AudioPlayerService.$inject = []
