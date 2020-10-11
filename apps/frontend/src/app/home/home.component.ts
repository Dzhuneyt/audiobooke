import {Component, OnInit, Optional} from '@angular/core';
import {Apollo} from 'apollo-angular';
import {AppSyncService} from '../app-sync.service';
import {AudiobookService} from '../services/audiobook.service';
import {BackendService} from '../services/backend.service';
import {UserService} from '../services/user.service';
import {Router} from '@angular/router';
import {Title} from '@angular/platform-browser';
import gql from 'graphql-tag';

interface Audiobook {
  id: string;
  title: string;
  description: string;
  cover_url: string;
}

interface Author {
  id: number;
  name: string;
  dob: number;
  dod: number;
}

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  public isLoggedIn;

  public top10audiobooks: Audiobook[] = [];

  public latestAuthors = [
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
    {name: 'Stephen King', dob: 1950, dod: 2030},
  ];

  constructor(
    @Optional() private userService: UserService,
    private audiobookService: AudiobookService,
    public router: Router,
    private title: Title,
    private appsync: AppSyncService,
    private apollo: Apollo,
  ) {
  }

  ngOnInit() {

    // this.apollo.watchQuery({
    //   query: gql`
    //     {
    //       audiobooks{
    //         id
    //       }
    //     }`
    // }).valueChanges.subscribe(value => {
    //   console.log(value);
    // });
    this.userService.user.subscribe(value => {
      this.isLoggedIn = value.isLoggedIn;
    });
    // If the user is already logged in, navigate to the audiobooks list
    // if (this.userService.getAccessToken()) {
    //   this.router.navigate(['/audiobook']);
    // }

    this.title.setTitle('Audibooke - Free audiobooks');

    this.audiobookService.getTop10().subscribe((value: Audiobook[]) => {
      this.top10audiobooks = value;
      console.log(value);
    });

    this.audiobookService.getLatestAuthors().subscribe(value => {
      this.latestAuthors = value;
    });
  }

  /**
   * @deprecated
   * @param provider
   */
  public signin(provider: String) {
    switch (provider) {
      case 'googleplus':
        this.userService.getGoogleSsoAuthUrl().subscribe(authUrl => {
          console.log(authUrl);
          window.location.replace(authUrl);
        });
        break;
    }
  }

}
