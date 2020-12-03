import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { AudioPlayerService } from './services/AudioPlayerService'
import { register as registerAudioPlayerComponent } from './components/AudioPlayer'

const MODULE_NAME = 'MusicLoud.Player2'

const playerModule = angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(AudioPlayerService.name, AudioPlayerService)

registerAudioPlayerComponent(playerModule)

export default MODULE_NAME
