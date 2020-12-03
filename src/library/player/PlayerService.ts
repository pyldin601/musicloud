import { action, makeObservable, observable, runInAction } from 'mobx'

enum PlayerStatus {
  Stopped = 'Stopped',
  Loading = 'Loading',
  Playing = 'Playing',
  Paused = 'Paused',
}

interface PlayerStoppedState {
  status: PlayerStatus.Stopped
}

interface PlayerPlayingState {
  status: PlayerStatus.Loading | PlayerStatus.Playing | PlayerStatus.Paused
  src: string
  currentTime: number
  bufferedTime: number
}

export type PlayerState = PlayerStoppedState | PlayerPlayingState

export class PlayerService {
  public state: PlayerState = {
    status: PlayerStatus.Stopped,
  }
  public appData: unknown = null

  private audio: HTMLAudioElement

  constructor() {
    this.audio = new Audio()

    this.audio.addEventListener('play', () => {
      this.setState({
        status: PlayerStatus.Loading,
        src: this.audio.src,
        currentTime: this.audio.currentTime,
        bufferedTime: this.bufferedTime,
      })
    })

    this.audio.addEventListener('timeupdate', () => {
      if (this.audio.paused) return

      this.setState({
        status: PlayerStatus.Playing,
        src: this.audio.src,
        currentTime: this.audio.currentTime,
        bufferedTime: this.bufferedTime,
      })
    })

    this.audio.addEventListener('ended', () => {
      this.audio.removeAttribute('src')

      runInAction(() => {
        this.setState({
          status: PlayerStatus.Stopped,
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

  public async play(src: string, appData: unknown | null): Promise<void> {
    this.setAppData(appData)
    this.audio.setAttribute('src', src)
    await this.audio.play()
  }

  public stop(): void {
    this.audio.pause()
    this.audio.removeAttribute('src')

    runInAction(() => {
      this.setState({
        status: PlayerStatus.Stopped,
      })
      this.setAppData(null)
    })
  }

  public pause(): void {
    this.audio.pause()
    this.setState({
      status: PlayerStatus.Paused,
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

  public setState(state: PlayerState): void {
    this.state = state
  }

  public setAppData(appData: unknown | null): void {
    this.appData = appData
  }
}

PlayerService.$inject = []
