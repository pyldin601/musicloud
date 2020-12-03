import { IModule } from 'angular'
import { computed, makeObservable } from 'mobx'
import templateUrl from './AudioPlayer.template.html'
import classes from './AudioPlayer.module.less'
import { AudioPlayerService, AudioPlayerState } from '../../services/AudioPlayerService'

export class AudioPlayerController {
  public readonly classes: typeof classes

  public get playerState(): AudioPlayerState {
    return this.audioPlayerService.state
  }

  constructor(readonly audioPlayerService: AudioPlayerService) {
    this.classes = classes

    makeObservable(this, {
      playerState: computed,
    })
  }
}

AudioPlayerController.$inject = [AudioPlayerService.name]

export function register(app: IModule): void {
  app.component('audioPlayer', {
    templateUrl,
    controller: AudioPlayerController,
  })
}
