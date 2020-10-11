import {NgModule} from '@angular/core';
import {ServerModule, ServerTransferStateModule} from '@angular/platform-server';

import {AppModule} from './app.module';
import {AppComponent} from './app.component';
import {FlexLayoutServerModule} from '@angular/flex-layout/server';

@NgModule({
  imports: [
    AppModule,
    ServerModule,
    ServerTransferStateModule,
    FlexLayoutServerModule
],
  providers: [
    // {
    //   provide: HTTP_INTERCEPTORS,
    //   useClass: UniversalInterceptor,
    //   multi: true
    // }
  ],
  bootstrap: [AppComponent],
})
export class AppServerModule {
}
