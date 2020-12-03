import { computed, makeObservable } from 'mobx'
import classes from './AudioPlayer.module.less'
import { AudioPlayerState } from '../../services/AudioPlayerService'
import { AudioPlayerQueueService } from '../../services/AudioPlayerQueueService'

export class AudioPlayerController {
  public readonly classes: typeof classes

  public get playerState(): AudioPlayerState {
    return this.audioPlayerQueueService.audioPlayerState
  }

  constructor(readonly audioPlayerQueueService: AudioPlayerQueueService) {
    this.classes = classes

    makeObservable(this, {
      playerState: computed,
    })
  }
}

AudioPlayerController.$inject = [AudioPlayerQueueService.name]
