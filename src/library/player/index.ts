import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { PlayerService } from './PlayerService'

const MODULE_NAME = 'MusicLoud.Player2'

export default angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(PlayerService.name, PlayerService)
