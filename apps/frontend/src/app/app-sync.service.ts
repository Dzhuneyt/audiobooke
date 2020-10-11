import {HttpClient} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {BackendService} from './services/backend.service';

@Injectable({
  providedIn: 'root'
})
export class AppSyncService {

  private readonly url = 'https://fe36spzuvvhnjmbwaikbq77gda.appsync-api.eu-west-1.amazonaws.com/graphql';
  private readonly apiKey = 'da2-koxzbf4jyrg6lcboblfhx3pl2a';

  constructor(protected http: HttpClient) {
  }

  request() {
    return this.http.post(this.url, `
      query {
        audiobooks{
          id
        }
      }
    `, {
      headers: {
        'x-api-key': this.apiKey
      }
    });
  }
}
