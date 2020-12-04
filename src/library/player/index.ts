import angular from 'angular'
import mobxAngularjs from 'mobx-angularjs'
import { AudioPlayerService } from './services/AudioPlayerService'
import { AudioPlayerQueueService } from './services/AudioPlayerQueueService'
import { PeakDataService } from './services/PeakDataService'
import { AudioPlayerComponent } from './components/AudioPlayerComponent'
import { AudioPlayerControls } from './components/AudioPlayerControls'
import { AudioPlayerPlayItem } from './components/AudioPlayerPlayItem'
// import { ActionPlayDirective } from './directives/ActionPlayDirective'

const MODULE_NAME = 'MusicLoud.Player2'

angular
  .module(MODULE_NAME, [mobxAngularjs])
  .service(AudioPlayerService.name, AudioPlayerService)
  .service(AudioPlayerQueueService.name, AudioPlayerQueueService)
  .service(PeakDataService.name, PeakDataService)
  // .directive(ActionPlayDirective)
  .component(AudioPlayerComponent)
  .component(AudioPlayerControls)
  .component(AudioPlayerPlayItem)

export default MODULE_NAME
