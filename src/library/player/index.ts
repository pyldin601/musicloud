import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { PlayerService } from './PlayerService'
import { register as registerAudioPlayerComponent } from './components/AudioPlayer'

const MODULE_NAME = 'MusicLoud.Player2'

const playerModule = angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(PlayerService.name, PlayerService)

registerAudioPlayerComponent(playerModule)

export default MODULE_NAME
