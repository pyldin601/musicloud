import { IModule } from 'angular'
import { computed, makeObservable } from 'mobx'
import templateUrl from './AudioPlayer.template.html'
import classes from './AudioPlayer.module.less'
import { PlayerService, PlayerState } from '../../PlayerService'

export class AudioPlayerController {
  public readonly classes: typeof classes

  public get playerState(): PlayerState {
    return this.playerService.state
  }

  constructor(readonly playerService: PlayerService) {
    this.classes = classes

    makeObservable(this, {
      playerState: computed,
    })
  }
}

AudioPlayerController.$inject = [PlayerService.name]

export function register(app: IModule): void {
  app.component('audioPlayer', {
    templateUrl,
    controller: AudioPlayerController,
  })
}
