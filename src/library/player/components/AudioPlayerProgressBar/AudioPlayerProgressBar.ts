import { IComponentOptions } from 'angular'
import { action, computed, makeObservable, observable } from 'mobx'
import makeDebug from 'debug'
import templateUrl from './AudioPlayerProgressBar.html'
import { AudioPlayerService, AudioPlayerStatus } from '../../services/AudioPlayerService'
import { AudioPlayerQueueService } from '../../services/AudioPlayerQueueService'
import { queuedReaction } from '../../../../utils/mobx'
import { PeakDataService } from '../../services/PeakDataService'

const COMPONENT_NAME = `audioPlayerProgressBar`

class AudioPlayerProgressBarController {
  private debug = makeDebug(AudioPlayerProgressBarController.name)

  static $inject = [
    AudioPlayerService.name,
    AudioPlayerQueueService.name,
    PeakDataService.name,
    '$element',
  ]

  private waveformElement = this.$element.find('.waveform-container')

  public peakData: ReadonlyArray<number> | null = null

  public get length(): number | null {
    const { state } = this.audioPlayerService

    if (state.status !== AudioPlayerStatus.Playing && state.status !== AudioPlayerStatus.Paused) {
      return null
    }

    return this.audioPlayerQueueService.currentEntry?.length ?? null
  }

  public get position(): number | null {
    const { state } = this.audioPlayerService

    if (state.status !== AudioPlayerStatus.Playing && state.status !== AudioPlayerStatus.Paused) {
      return null
    }

    return state.currentTime
  }

  public get isPlayerLoaded(): boolean {
    const { state } = this.audioPlayerService
    return state.status === AudioPlayerStatus.Playing || state.status === AudioPlayerStatus.Paused
  }

  public get isPeakDataLoaded(): boolean {
    return this.peakData !== null
  }

  public get positionInPercent(): number {
    const playerState = this.audioPlayerService.state
    if (
      playerState.status !== AudioPlayerStatus.Playing &&
      playerState.status !== AudioPlayerStatus.Paused
    ) {
      return 0
    }

    const position = playerState.currentTime
    const length = this.audioPlayerQueueService.currentEntry?.length

    if (!length) {
      return 0
    }

    return (100 / length) * position
  }

  constructor(
    private readonly audioPlayerService: AudioPlayerService,
    private readonly audioPlayerQueueService: AudioPlayerQueueService,
    private readonly peakDataService: PeakDataService,
    private readonly $element: JQLite,
  ) {
    makeObservable(this, {
      length: computed,
      position: computed,
      isPlayerLoaded: computed,
      isPeakDataLoaded: computed,
      peakData: observable,
      setPeakData: action,
    })

    queuedReaction(
      () => this.audioPlayerQueueService.currentEntry,
      async (currentEntry) => {
        this.setPeakData(null)
        if (!currentEntry) return
        const peakData = await this.peakDataService.getPeakData(currentEntry.trackId)
        this.debug('getPeakData', { peakData })
        this.setPeakData(peakData)
      },
    )

    this.debug('Ready')
  }

  public setPeakData(peakData: ReadonlyArray<number> | null): void {
    this.peakData = peakData
  }

  public async handleMouseDown(event: MouseEvent): Promise<void> {
    const offset = this.waveformElement.offset()
    const width = this.waveformElement.width()
    if (!offset || !width) return
    await this.audioPlayerQueueService.seekPercent((100 / width) * (event.clientX - offset.left))
  }
}

export const AudioPlayerProgressBar: Record<typeof COMPONENT_NAME, IComponentOptions> = {
  [COMPONENT_NAME]: {
    templateUrl,
    controller: AudioPlayerProgressBarController,
  },
}
