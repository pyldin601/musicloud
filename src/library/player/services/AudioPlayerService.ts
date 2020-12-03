import { action, makeObservable, observable, runInAction } from 'mobx'

enum AudioPlayerStatus {
  Stopped = 'Stopped',
  Loading = 'Loading',
  Playing = 'Playing',
  Paused = 'Paused',
}

interface AudioPlayerStoppedState {
  status: AudioPlayerStatus.Stopped
}

interface AudioPlayerPlayingState {
  status: AudioPlayerStatus.Loading | AudioPlayerStatus.Playing | AudioPlayerStatus.Paused
  src: string
  currentTime: number
  bufferedTime: number
}

export type AudioPlayerState = AudioPlayerStoppedState | AudioPlayerPlayingState

export class AudioPlayerService<T extends unknown = unknown> {
  public state: AudioPlayerState = {
    status: AudioPlayerStatus.Stopped,
  }
  public appData: T | null = null

  private audio: HTMLAudioElement

  constructor() {
    this.audio = new Audio()

    this.audio.addEventListener('play', () => {
      this.setState({
        status: AudioPlayerStatus.Loading,
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

      runInAction(() => {
        this.setState({
          status: AudioPlayerStatus.Stopped,
        })
        this.setAppData(null)
      })
    })

    makeObservable(this, {
      appData: observable,
      setAppData: action,
      state: observable,
      setState: action,
    })
  }

  private get bufferedTime(): number {
    return this.audio.buffered.length > 0 ? this.audio.buffered.end(0) : 0
  }

  public async play(src: string, appData: T | null): Promise<void> {
    this.setAppData(appData)
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
      this.setAppData(null)
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

  public setAppData(appData: T | null): void {
    this.appData = appData
  }
}

AudioPlayerService.$inject = []
