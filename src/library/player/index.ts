import angular from 'angular'
import { PlayerService } from './PlayerService'

const MODULE_NAME = 'MusicLoud.Player2'

const player = angular.module(MODULE_NAME, [])

player.service(PlayerService.name, PlayerService)

export default MODULE_NAME
