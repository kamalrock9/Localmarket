import { LoadingProvider } from "./../providers/loading/loading";
import { AnalyticsService } from "./../providers/analytics-service/analytics-service";
import { InAppBrowser } from "@ionic-native/in-app-browser";
import { AppRate } from "@ionic-native/app-rate";
import { AppVersion } from "@ionic-native/app-version";
import { EmailComposer } from "@ionic-native/email-composer";
import { App } from "./app.config";
import {
  RestProvider,
  SettingsProvider,
  NotifProvider,
  WooCommerceProvider,
  ToastProvider,
  UserProvider
} from "./../providers/providers";
import { OneSignal } from "@ionic-native/onesignal";
import { Component, NgZone, ViewChild } from "@angular/core";
import {
  Platform,
  Events,
  ModalController,
  AlertController,
  Nav,
  IonicApp,
  MenuController
} from "ionic-angular";
import { StatusBar } from "@ionic-native/status-bar";
import { TranslateService } from "@ngx-translate/core";
import { ImageLoaderConfig } from "ionic-image-loader";
import { AdMobFree } from "@ionic-native/admob-free";

@Component({
  templateUrl: "app.html"
})
export class MyApp {
  rootPage: any; //='MenuPage';
  setting: any = {};
  @ViewChild(Nav) nav: Nav;
  version: string;
  lastBack: any;
  appSettings;
  y: boolean;
  z: boolean = false;

  constructor(
    private admob: AdMobFree,
    public platform: Platform,
    private statusBar: StatusBar,
    public oneSignal: OneSignal,
    private zone: NgZone,
    public settings: SettingsProvider,
    public translate: TranslateService,
    private events: Events,
    public rest: RestProvider,
    private toast: ToastProvider,
    private modalCtrl: ModalController,
    private emailComposer: EmailComposer,
    private appRate: AppRate,
    private alertCtrl: AlertController,
    private appVersion: AppVersion,
    private WC: WooCommerceProvider,
    public user: UserProvider,
    ionicApp: IonicApp,
    menuCtrl: MenuController,
    private notif: NotifProvider,
    private iab: InAppBrowser,
    private anlyticsService: AnalyticsService,
    private loader: LoadingProvider,
    private imageLoaderConfig: ImageLoaderConfig
  ) {
    this.settings.load().then(res => {
      this.setting = res;
      this.initTranslate();
    });

    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      //image loader configs
      imageLoaderConfig.enableSpinner(false);
      this.imageLoaderConfig.setFallbackUrl(
        "assets/imgs/placeholder-square.png"
      );
      this.imageLoaderConfig.setMaximumCacheAge(7 * 24 * 60 * 60 * 1000); //7days
      this.imageLoaderConfig.enableFallbackAsPlaceholder(true);
      this.imageLoaderConfig.setFileNameCachedWithExtension(true);
      this.imageLoaderConfig.setImageReturnType("base64");

      if (this.platform.is("cordova")) {
        events.subscribe("cartchanged", () => {
          rest.getCartCount().then(res => {
            console.log(res);
            settings.setSettings(JSON.parse(res.data), "cart_count");
          });
        });
      }
      this.initApp();
      statusBar.styleLightContent();
      if (platform.is("android")) {
        statusBar.overlaysWebView(false);
        platform.registerBackButtonAction(() => {
          let activePortal =
            ionicApp._loadingPortal.getActive() ||
            ionicApp._modalPortal.getActive() ||
            ionicApp._toastPortal.getActive() ||
            ionicApp._overlayPortal.getActive();
          if (activePortal) {
            activePortal.dismiss();
          } else if (menuCtrl.isOpen()) {
            menuCtrl.close();
          } else {
            if (this.nav.canGoBack()) {
              this.nav.pop();
            } else if (this.lastBack + 300 < Date.now()) {
              this.exit();
            }
          }
          this.lastBack = Date.now();
        });
      } else {
        statusBar.overlaysWebView(true);
      }
      //this.rootPage="MenuPage";
    });
  }

  initTranslate() {
    if (this.setting && this.setting.language !== undefined) {
      this.translate.setDefaultLang(this.setting.language);
      this.translate.use(this.setting.language);
    } else {
      this.translate.setDefaultLang(App.defaultLang);
      this.translate.use(App.defaultLang);
    }
  }
  initApp() {
    this.WC.saveAppSettings().subscribe(
      res => {
        console.log(res);
        this.settings.setSettings(res, "appSettings").then(() => {
          console.log(this.settings.all);
          const colors = new Map([
            ["primary_color", this.settings.all.appSettings.primary_color],
            [
              "primary_color_dark",
              this.settings.all.appSettings.primary_color_dark
            ],
            ["accent_color", this.settings.all.appSettings.accent_color],
            [
              "toolbar_text_color",
              this.settings.all.appSettings.primary_color_text
            ],
            [
              "toolbar-badge-color",
              this.settings.all.appSettings.toolbarbadgecolor ||
                this.settings.all.appSettings.accent_color
            ],
            ["primary_text_color", "#212121"],
            ["secondary_text_color", "#757575"]
          ]);
          Array.from(colors.entries()).forEach(([name, value]) => {
            document.body.style.setProperty(`--${name}`, value);
          });
          this.zone.run(() => {
            this.rootPage = "HomePage";
          });
          if (this.platform.is("cordova")) {
            this.events.publish("cartchanged");
            this.statusBar.backgroundColorByHexString(
              this.settings.all.appSettings.primary_color_dark
            );
            this.appVersion.getVersionNumber().then(v => {
              this.version = v;
              if (
                this.settings.all.appSettings.google_analytics_tracker_id &&
                this.settings.all.appSettings.google_analytics_tracker_id !== ""
              ) {
                this.anlyticsService
                  .init(
                    this.settings.all.appSettings.google_analytics_tracker_id
                  )
                  .then(() => {
                    //console.log('Google analytics is ready now');
                    this.events.subscribe("view:enter", (view: string) => {
                      this.anlyticsService.trackView(view);
                      console.log("view:enter" + view);
                    });
                    this.events.publish("view:enter", "Home Page");
                    this.anlyticsService.setAppVersion("Android - " + v);
                  })
                  .catch(e => console.log("Error starting GoogleAnalytics", e));
              }
            });

            if (
              this.settings.all.appSettings.one_signal_app_id &&
              this.settings.all.appSettings.one_signal_app_id != "" &&
              this.settings.all.appSettings.google_project_number &&
              this.settings.all.appSettings.google_project_number != ""
            ) {
              this.oneSignal.startInit(
                this.settings.all.appSettings.one_signal_app_id,
                this.settings.all.appSettings.google_project_number
              );
              this.oneSignal.inFocusDisplaying(
                this.oneSignal.OSInFocusDisplayOption.Notification
              );
              this.oneSignal.handleNotificationReceived().subscribe(x => {
                // do something when notification is received
                console.log(x);
                this.notif.post(x.payload);
              });
              this.oneSignal.handleNotificationOpened().subscribe(() => {
                // do something when a notification is opened
              });
              this.oneSignal.endInit();
            }
          }
        });
      },
      err => {
        console.log(err);
        this.toast.showError();
      }
    );
  }

  goTo(page, params) {
    this.nav.push(page, { params: params });
  }
  popToRoot() {
    this.nav.popToRoot();
  }

  login() {
    this.modalCtrl.create("LoginPage", {}).present();
  }
  email() {
    this.platform.ready().then(() => {
      if (this.platform.is("cordova")) {
        let email = {
          to: this.settings.appSettings.contact_email,
          subject: "App Support",
          body: "Hi, please help me.",
          isHtml: true
        };
        this.emailComposer.open(email);
      } else {
        this.translate
          .get(["ONLY_DEVICE", "ONLY_DEVICE_DESC", "OK"])
          .subscribe(x => {
            this.alertCtrl
              .create({
                title: x.ONLY_DEVICE,
                message: x.ONLY_DEVICE_DESC,
                buttons: [
                  {
                    text: x.OK
                  }
                ]
              })
              .present();
          });
      }
    });
  }
  call() {
    window.location.href = "tel:" + this.settings.appSettings.contact_phone;
  }
  rate() {
    if (!this.platform.is("cordova")) {
      this.translate
        .get(["OK", "ONLY_DEVICE", "ONLY_DEVICE_DESC"])
        .subscribe(x => {
          this.alertCtrl
            .create({
              title: x.ONLY_DEVICE,
              message: x.ONLY_DEVICE_DESC,
              buttons: [{ text: x.OK }]
            })
            .present();
          return false;
        });
    } else {
      this.appVersion.getAppName().then(res => {
        this.appRate.preferences.displayAppName = res;
      });

      this.appVersion.getPackageName().then(res => {
        this.appRate.preferences.storeAppURL = {
          ios: App.IosAppId, // FOR IOS USE APPLE ID from appstoreconnect
          android: "market://details?id=" + res // FOR ANDROID, use your own android package name
        };
        this.appRate.promptForRating(true);
      });
    }
  }

  showAdmobBannerAds() {
    this.admob.interstitial.config({
      id: "ca-app-pub-2336008794991646/4281109684",
      isTesting: false,
      autoShow: true
    });
    this.admob.on(this.admob.events.INTERSTITIAL_LOAD_FAIL).subscribe(() => {
      this.platform.exitApp();
    });
    this.admob.on(this.admob.events.INTERSTITIAL_CLOSE).subscribe(() => {
      this.platform.exitApp();
    });
    this.admob.interstitial.prepare();
  }

  exit() {
    this.translate
      .get(["CONFIRM", "EXIT_MSG", "EXIT", "CANCEL"])
      .subscribe(x => {
        this.alertCtrl
          .create({
            title: x.CONFIRM,
            message: x.EXIT_MSG,
            buttons: [
              {
                text: x.EXIT + "?",
                handler: () => {
                  //this.showAdmobBannerAds();
                  this.platform.exitApp();
                }
              },
              {
                text: "Cancel",
                role: x.CANCEL
              }
            ]
          })
          .present();
      });
  }
  openChat() {
    if (
      this.settings.all.appSettings &&
      this.settings.all.appSettings.direct_tawk_id &&
      this.settings.all.appSettings.direct_tawk_id !== ""
    ) {
      this.loader.show();
      const browser = this.iab.create(
        this.settings.all.appSettings.direct_tawk_id,
        "_self",
        { location: "no", closebuttoncaption: "Done", hidden: "yes" }
      );
      browser.on("loadstop").subscribe(event => {
        browser.show();
        this.loader.dismiss();
      });
    }
  }
  contact() {
    //this.iab.create(App.url + "/contact-us/", '_system', { location: 'no', closebuttoncaption: 'Done' });
    if (
      this.settings.appSettings.contact_email &&
      this.settings.appSettings.contact_phone &&
      this.settings.appSettings.contact_email !== "" &&
      this.settings.appSettings.contact_phone !== ""
    ) {
      this.alertCtrl
        .create({
          title: "Contact us",
          cssClass: "custom_alert_call",
          buttons: [
            {
              text: "Email",
              handler: () => {
                this.email();
              }
            },
            {
              text: "Call",
              handler: () => {
                this.call();
              }
            }
          ]
        })
        .present();
    } else if (this.settings.appSettings.contact_email !== "") {
      this.email();
    } else {
      this.call();
    }
  }
  order_request() {
    this.iab.create(
      "https://wa.me/" +
        this.settings.appSettings.contact_phone.replace(/\+/g, ""),
      "_system",
      { location: "no", closebuttoncaption: "Done" }
    );
    // window.location.href = 'https://api.whatsapp.com/send?phone= ' + this.settings.appSettings.contact_phone;
  }

  rewardVideo() {
    let videoStarted = true;
    this.loader.show();
    this.admob.rewardVideo.config({
      id: "ca-app-pub-2336008794991646/3460590790",
      isTesting: false,
      autoShow: true
    });
    this.admob.on(this.admob.events.REWARD_VIDEO_LOAD_FAIL).subscribe(() => {
      this.toast.show("Something went wrong try again later.");
      videoStarted = false;
      this.loader.dismiss();
    });
    this.admob.on(this.admob.events.REWARD_VIDEO_OPEN).subscribe(() => {
      this.loader.dismiss();
    });
    this.admob.on(this.admob.events.REWARD_VIDEO_REWARD).subscribe(() => {
      if (videoStarted) {
        this.toast.show("Provide Rewards");

        this.rest
          .walletReward()
          .then(res => {
            let data = JSON.parse(res.data);
            if (data && data.message) {
              this.toast.show(data.message);
            }
          })
          .catch(err => {
            console.log(err);
            this.toast.show("Something went wrong try later");
          });
      }
      videoStarted = false;
    });

    this.admob.rewardVideo.prepare();
  }
}
