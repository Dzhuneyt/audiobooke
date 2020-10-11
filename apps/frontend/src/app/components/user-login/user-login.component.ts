import {Component, Inject, OnInit, Optional, PLATFORM_ID} from '@angular/core';
import {UserService} from '../../services/user.service';
import {ActivatedRoute, Router} from '@angular/router';
import {Title} from '@angular/platform-browser';
import {LocalStorageService} from 'angular-2-local-storage';
import {isPlatformServer} from '@angular/common';

@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.scss']
})
export class UserLoginComponent implements OnInit {

  public showSsoButtons = true;

  constructor(
    private userService: UserService,
    @Optional() private router: Router,
    private titleService: Title,
    @Optional() private localStorageService: LocalStorageService,
    @Inject(PLATFORM_ID) private platformId: string,
    private activatedRoute: ActivatedRoute) {

  }

  ngOnInit() {
    this.titleService.setTitle('Login or Register');

    this.isSsoCallbackContext().then(async value => {
      if (value) {
        await this.handleCallbackParams();
      } else {
        this.showSsoButtons = true;
      }
    });
  }

  /**
   * Return whether we are in a state where the SSO provider
   * (Google) just redirected us back and attached some query params
   */
  private async isSsoCallbackContext(): Promise<boolean> {
    if (isPlatformServer(this.platformId)) {
      console.log('In Angular Universal. Not SSO callback context');
      return false;
    }

    return new Promise((resolve, reject) => {
      this.activatedRoute.queryParamMap.subscribe(value => {
        if (!value.has('code')) {
          resolve(false);
          return;
        }
        resolve(true);
      }, error => {
        reject(error);
      });
    });

  }


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

  private handleCallbackParams() {
    this.activatedRoute.queryParamMap.subscribe(params => {
      this.userService.finishGoogleSsoAuth({
        code: params.get('code'),
        state: params.get('state'),
      }).then(success => {
        if (!success) {
          console.error('Failed to login');
          this.showSsoButtons = true;
          return;
        }

        // Login success. Redirect after login to a useful place
        this.successRedirect();
      });
    });


  }

  private successRedirect() {
    const lastUrl = this.localStorageService.get('last_url');
    if (lastUrl) {
      console.log('Redirecting to ', lastUrl);
      this.router.navigate([lastUrl]);
    } else {
      console.log('Redirecting to homepage');
      this.router.navigate(['audiobook']);
    }
  }
}
