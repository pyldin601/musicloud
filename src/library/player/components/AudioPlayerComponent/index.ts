import { IComponentOptions } from 'angular'
import { AudioPlayerController } from './AudioPlayer.controller'
import templateUrl from './AudioPlayer.template.html'

export const AudioPlayerComponent: Record<string, IComponentOptions> = {
  audioPlayer: {
    templateUrl,
    controller: AudioPlayerController,
  },
}
