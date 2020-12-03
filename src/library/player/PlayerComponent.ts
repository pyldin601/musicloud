import templateUrl from './PlayerComponent.html'
import { IModule } from 'angular'

export class PlayerComponent {}

PlayerComponent.$inject = []

export function usePlayerComponent(app: IModule): void {
  app.component('playerComponent', {
    templateUrl,
    controller: PlayerComponent,
  })
}
