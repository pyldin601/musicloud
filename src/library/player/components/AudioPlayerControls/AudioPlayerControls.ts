import templateUrl from './AudioPlayerControls.html'
import { AudioPlayerQueueService } from '../../services/AudioPlayerQueueService'
import { computed, makeObservable } from 'mobx'

const COMPONENT_NAME = 'audioPlayerControls'

class AudioPlayerControlsController {
  constructor(private readonly audioPlayerQueueService: AudioPlayerQueueService) {
    makeObservable(this, {
      isPlaying: computed,
      isBuffering: computed,
    })
  }

  public get isPlaying(): boolean {
    return this.audioPlayerQueueService.isPlaying
  }

  public get isBuffering(): boolean {
    return this.audioPlayerQueueService.isLoading
  }

  public playPrevious(): void {
    this.audioPlayerQueueService.playPrevious()
  }

  public playNext(): void {
    this.audioPlayerQueueService.playNext()
  }

  public playOrPause(): void {
    if (this.audioPlayerQueueService.isPaused || this.audioPlayerQueueService.isPlaying) {
      this.audioPlayerQueueService.playPause()
    }
  }
}

AudioPlayerControlsController.$inject = [AudioPlayerQueueService.name]

export const AudioPlayerControls = {
  [COMPONENT_NAME]: {
    templateUrl,
    controller: AudioPlayerControlsController,
  },
}
