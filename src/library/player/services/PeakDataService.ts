import { IHttpService } from 'angular'

export type PeakData = ReadonlyArray<number>

export class PeakDataService {
  constructor(private $http: IHttpService) {}

  public async getPeakData(trackId: string): Promise<PeakData> {
    const response = await this.$http.get<PeakData>(`/peaks/${trackId}`)
    return response.data
  }
}

PeakDataService.$inject = ['$http']
