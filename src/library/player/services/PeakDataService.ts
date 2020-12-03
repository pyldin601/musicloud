import { IHttpService } from 'angular'

export class PeakDataService {
  constructor(private $http: IHttpService) {}

  public async getPeakData(trackId: string): Promise<ReadonlyArray<number>> {
    const response = await this.$http.get<ReadonlyArray<number>>(`/peaks/${trackId}`)
    return response.data
  }
}

PeakDataService.$inject = ['$http']
