import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { PlayerService } from './PlayerService'
import { usePlayerComponent } from './PlayerComponent'

const MODULE_NAME = 'MusicLoud.Player2'

const playerModule = angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(PlayerService.name, PlayerService)

usePlayerComponent(playerModule)

export default MODULE_NAME
