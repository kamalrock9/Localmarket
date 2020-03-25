import { AndroidPermissions } from '@ionic-native/android-permissions';
import { FileTransfer } from '@ionic-native/file-transfer';
import { File } from '@ionic-native/file';
import { InAppBrowser } from '@ionic-native/in-app-browser';
import { EmailComposer } from '@ionic-native/email-composer';
import { AppVersion } from '@ionic-native/app-version';
import { AppRate } from '@ionic-native/app-rate';
import { SocialSharing } from '@ionic-native/social-sharing';
import { OneSignal } from '@ionic-native/onesignal';
import { IonicStorageModule } from '@ionic/storage';
import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule, Injector } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { MyApp } from './app.component';
import { UserProvider, ToastProvider, SettingsProvider, LoadingProvider, NotifProvider, WooCommerceProvider, WishlistProvider,  RestProvider, AnalyticsService } from '../providers/providers';
import { TranslateModule, TranslateLoader } from '@ngx-translate/core';
import { TranslateHttpLoader } from '@ngx-translate/http-loader';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';
import { GooglePlus } from '@ionic-native/google-plus';
import { Facebook } from '@ionic-native/facebook';
import { GoogleAnalytics } from '@ionic-native/google-analytics';
import { PhotoViewer } from '@ionic-native/photo-viewer';
import { AdMobFree } from '@ionic-native/admob-free';
import { IonicImageLoader } from 'ionic-image-loader';
import { Deeplinks } from '@ionic-native/deeplinks';

export function createTranslateLoader(http: HttpClient) {
  console.log("TranslateLoader");
  return new TranslateHttpLoader(http, './assets/i18n/', '.json');
}

@NgModule({
  declarations: [
    MyApp
  ], 
  imports: [
    HttpClientModule,
    BrowserModule,
    TranslateModule.forRoot({
      loader: {
        provide: TranslateLoader,
        useFactory: (createTranslateLoader),
        deps: [HttpClient]
      }
    }),
    IonicStorageModule.forRoot({
      name: 'localmt',
      driverOrder: ['sqlite', 'websql', 'indexeddb']
    }),
    IonicImageLoader.forRoot(),

    IonicModule.forRoot(MyApp, {
      backButtonText: '',
      mode: 'md',
      preloadModules: true
    })
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp
  ],
  providers: [
    // Keep this to enable Ionic's runtime error handling during development
    { provide: ErrorHandler, useClass: IonicErrorHandler },
    RestProvider,
    UserProvider,
    WooCommerceProvider,
    SettingsProvider,
    WishlistProvider,
    LoadingProvider,
    NotifProvider,
    ToastProvider,
    RestProvider,
    IonicStorageModule,
    StatusBar,
    SplashScreen,
    OneSignal,
    SocialSharing,
    AppRate,
    AppVersion, 
    EmailComposer,
    InAppBrowser,
    HTTP,
    NotifProvider,
    GooglePlus,
    Facebook,
    GoogleAnalytics,
    AnalyticsService,
    PhotoViewer ,
    File,
    FileTransfer,
    AndroidPermissions,
    AdMobFree,
    Deeplinks
  ]
})
export class AppModule {
  public static injector: Injector;
  constructor(injector: Injector) {
    AppModule.injector = injector;
  }
}