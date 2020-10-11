import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';
import {AppComponent} from './app.component';
import {RouterModule} from '@angular/router';
import {APP_ROUTES} from './app.routing';
import {UniversalInterceptor} from './server/universal-interceptor.service';
import {BackendService} from './services/backend.service';
import {HTTP_INTERCEPTORS, HttpClientModule} from '@angular/common/http';
import {AudiobookService} from './services/audiobook.service';
import {UserLoginComponent} from './components/user-login/user-login.component';
import {UserService} from './services/user.service';
import {LocalStorageModule} from 'angular-2-local-storage';
import {HeaderComponent} from './components/header/header.component';
import {LoggedInUserGuard} from './guards/logged-in-user.guard';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {SidebarComponent} from './components/sidebar/sidebar.component';
import {FlexLayoutModule} from '@angular/flex-layout';
import {FooterComponent} from './footer/footer.component';
import {PrivacyPolicyComponent} from './privacy-policy/privacy-policy.component';
import {HomeComponent} from './home/home.component';
import {AnalyticsService} from './analytics.service';
import {TransferHttpCacheModule} from '@nguniversal/common';
import {MaterialModule} from './modules/material.module';
import { GraphQLModule } from './graphql.module';

@NgModule({
  declarations: [
    // Components
    AppComponent,
    UserLoginComponent,

    HeaderComponent,
    SidebarComponent,
    FooterComponent,
    PrivacyPolicyComponent,
    HomeComponent,
  ],
  imports: [
    // Other modules
    BrowserModule.withServerTransition({appId: 'serverApp'}),
    HttpClientModule,

    // Avoid making duplicated API calls both on SSR and the Client
    // @see https://github.com/angular/universal/blob/master/docs/transfer-http.md
    TransferHttpCacheModule,
    // BrowserTransferStateModule,

    LocalStorageModule.forRoot({
      storageType: 'localStorage',
      prefix: 'my-app'
    }),
    RouterModule.forRoot(APP_ROUTES, {
      initialNavigation: 'enabled'
    }),
    BrowserAnimationsModule,
    FlexLayoutModule.withConfig({
      ssrObserveBreakpoints: ['xs', 'lt-md'],
      useColumnBasisZero: false,
      printWithBreakpoints: ['md', 'lt-lg', 'lt-xl', 'gt-sm', 'gt-xs']
    }),
    MaterialModule,
    GraphQLModule,
  ],
  providers: [
    // Services
    BackendService,
    AudiobookService,
    UserService,
    LoggedInUserGuard,
    AnalyticsService,
    {
      provide: HTTP_INTERCEPTORS,
      useClass: UniversalInterceptor,
      multi: true
    }
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
}
