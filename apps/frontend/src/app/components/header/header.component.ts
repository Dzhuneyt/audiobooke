import {Component, EventEmitter, OnInit, Output} from '@angular/core';
import {UserService} from '../../services/user.service';
import {Router} from '@angular/router';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent implements OnInit {

  @Output() drawerClick = new EventEmitter();

  // @TODO implement drawer
  public showDrawer = false;

  constructor(
    public router: Router,
    private userService: UserService
  ) {
  }

  ngOnInit() {
  }

  isLoggedIn(): boolean {
    return this.userService.isLoggedIn();
  }

  logout() {
    this.userService.logout();
    this.router.navigate(['user/login']);
  }

}
