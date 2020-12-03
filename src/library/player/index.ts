import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { AudioPlayerService } from './services/AudioPlayerService'
import { AudioPlayerQueueService } from './services/AudioPlayerQueueService'
import { register as registerAudioPlayerComponent } from './components/AudioPlayer'

const MODULE_NAME = 'MusicLoud.Player2'

const playerModule = angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(AudioPlayerService.name, AudioPlayerService)
  .service(AudioPlayerQueueService.name, AudioPlayerQueueService)

registerAudioPlayerComponent(playerModule)

export default MODULE_NAME
