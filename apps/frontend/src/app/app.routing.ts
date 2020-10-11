import {Routes} from '@angular/router';
import {UserLoginComponent} from './components/user-login/user-login.component';
import {PrivacyPolicyComponent} from './privacy-policy/privacy-policy.component';

export const APP_ROUTES: Routes = [
  {
    path: '',
    loadChildren: () => import('./modules/home.module').then(m => m.HomeModule),
  },
  {
    path: 'audiobook',
    loadChildren: () => import('./modules/audiobook.module').then(m => m.AudiobookModule)
  },
  {
    path: 'user/login',
    component: UserLoginComponent,
  },
  {
    path: 'privacy',
    component: PrivacyPolicyComponent,
  },
  {
    path: '',
    redirectTo: '/audiobook',
    pathMatch: 'full'
  },
];
