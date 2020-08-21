import { App } from "./../../app/app.config";
import { Facebook, FacebookLoginResponse } from "@ionic-native/facebook";
import { TranslateService } from "@ngx-translate/core";
import {
  UserProvider,
  LoadingProvider,
  ToastProvider,
  SettingsProvider,
} from "./../../providers/providers";
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import { Component, ViewChild } from "@angular/core";
import {
  IonicPage,
  NavController,
  NavParams,
  ViewController,
  Platform,
  AlertController,
} from "ionic-angular";
import { GooglePlus } from "@ionic-native/google-plus";

@IonicPage({
  priority: "low",
})
@Component({
  selector: "page-login",
  templateUrl: "login.html",
})
export class LoginPage {
  @ViewChild("textInput") textInput;
  loginForm: FormGroup;
  resetForm: FormGroup;
  signupForm: FormGroup;
  referralForm: FormGroup;
  page: string;
  dir: string;
  storeName: any = { value: App.store };
  currentUserId: number;
  signupby: string;

  constructor(
    private alertCtrl: AlertController,
    public navCtrl: NavController,
    public navParams: NavParams,
    public viewCtrl: ViewController,
    private settings: SettingsProvider,
    private formBuilder: FormBuilder,
    private user: UserProvider,
    private loader: LoadingProvider,
    platform: Platform,
    private toast: ToastProvider,
    private translate: TranslateService,
    private googlePlus: GooglePlus,
    private fb: Facebook
  ) {
    this.dir = platform.dir();
    this.page = this.navParams.data.page || "login";
    this.loginForm = this.formBuilder.group({
      user: ["", Validators.required],
      pass: ["", Validators.required],
    });
    this.resetForm = this.formBuilder.group({
      email: ["", Validators.email],
    });
    this.signupForm = this.formBuilder.group({
      fname: ["", Validators.required],
      lname: ["", Validators.required],
      email: ["", Validators.required],
      pass: ["", Validators.required],
      pass2: ["", Validators.required],
    });
    this.referralForm = this.formBuilder.group({
      refer_code: ["", Validators.required],
    });
  }

  ionViewDidLoad() {
    console.log("ionViewDidLoad LoginPage");
  }
  dismiss(skip) {
    if (skip) {
      if (this.signupby === "social") {
        this.translate
          .get(["LOGIN_SUCCESS"], {
            value: this.user.first_name || this.user.username,
          })
          .subscribe((x) => {
            this.toast.show(x.LOGIN_SUCCESS);
          });
        this.viewCtrl.dismiss({ code: true });
      } else {
        this.translate.get(["REGISTER_SUCCESS"]).subscribe((x) => {
          this.toast.show(x.REGISTER_SUCCESS);
        });
        this.goToLogin();
      }
    } else {
      this.viewCtrl.dismiss();
    }
  }
  goToLogin() {
    this.page = "login";
  }
  goToReferral() {
    this.page = "referral";
  }
  goToSignup() {
    this.page = "signup";
    // this.removeFocus();
  }
  goToReset() {
    this.page = "reset";
  }
  removeFocus() {
    setTimeout(() => {
      this.textInput.setFocus();
    }, 150);
  }

  login() {
    console.log(this.loginForm.value);
    if (!this.loginForm.valid) {
      this.translate.get(["FILL_REQUIRED_FIELD"]).subscribe((x) => {
        this.toast.show(x.FILL_REQUIRED_FIELD);
      });
      return;
    }
    this.loader.show();
    this.user.login(this.loginForm.value).subscribe(
      (res: any) => {
        console.log(res);
        this.loader.dismiss();
        if (res.code == 1) {
          this.translate
            .get(["LOGIN_SUCCESS"], {
              value: res.details.first_name || res.details.username,
            })
            .subscribe((x) => {
              this.toast.show(x.LOGIN_SUCCESS);
            });
          this.user.loggedIn(res.details);
          this.viewCtrl.dismiss({ code: true });
        } else {
          this.translate.get(["LOGIN_FAILED"]).subscribe((x) => {
            this.toast.show(x.LOGIN_FAILED);
          });
        }
      },
      (err) => {
        this.loader.dismiss();
        console.error(err);
        this.toast.show(err.code);
      }
    );
  }
  register() {
    if (!this.signupForm.valid) {
      this.translate.get(["FILL_REQUIRED_FIELD"]).subscribe((x) => {
        this.toast.show(x.FILL_REQUIRED_FIELD);
      });
      return;
    }

    if (!this.signupForm.value.fname.match(/^([^0-9]*)$/)) {
      this.translate.get(["NAME_CAN_ONLY_CONTAIN_LETTERS"]).subscribe((x) => {
        this.toast.show(x.NAME_CAN_ONLY_CONTAIN_LETTERS);
      });
      return;
    }

    if (!this.signupForm.value.lname.match(/^([^0-9]*)$/)) {
      this.translate.get(["NAME_CAN_ONLY_CONTAIN_LETTERS"]).subscribe((x) => {
        this.toast.show(x.NAME_CAN_ONLY_CONTAIN_LETTERS);
      });
      return;
    }

    if (
      !this.signupForm.value.email.match(
        /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/
      )
    ) {
      this.translate.get(["PLEASE_ENTER_THE_VALID_EMAIL"]).subscribe((x) => {
        this.toast.show(x.PLEASE_ENTER_THE_VALID_EMAIL);
      });
      return;
    }
    if (this.signupForm.value.pass !== this.signupForm.value.pass2) {
      this.translate.get(["PASSWORD_NOT_MATCH"]).subscribe((x) => {
        this.toast.show(x.PASSWORD_NOT_MATCH);
      });
      return;
    }
    this.loader.show();
    this.user.register(this.signupForm.value).subscribe(
      (res: any) => {
        console.log(res);
        this.loader.dismiss();
        if (res.status == "1") {
          if (this.settings.appSettings.referearn && res.refer_earn == "1") {
            this.signupby = "form";
            this.currentUserId = res.user_id;
            this.goToReferral();
          } else {
            this.translate.get(["REGISTER_SUCCESS"]).subscribe((x) => {
              this.toast.show(x.REGISTER_SUCCESS);
            });
            this.goToLogin();
          }
        } else {
          //this.translate.get(['REGISTER_FAILED'], { value: res.firstname + res.lastname }).subscribe((x) => {
          this.toast.showWithClose(res.error);
          //});
        }
      },
      (err) => {
        this.loader.dismiss();
        console.error(err);
        this.toast.show(err.code);
      }
    );
  }
  reset() {
    if (!this.resetForm.valid) {
      this.translate.get(["WRONG_EMAIL"]).subscribe((x) => {
        this.toast.show(x.WRONG_EMAIL);
      });
      return;
    }
    this.loader.show();
    this.user.reset(this.resetForm.value).subscribe(
      (res: any) => {
        this.loader.dismiss();
        this.toast.show(res.message);
        if (res.code == 1) {
          this.goToLogin();
        }
      },
      (err) => {
        this.loader.dismiss();
        console.error(err);
        this.toast.show(err.code);
      }
    );
  }
  googleLogin() {
    this.loader.show();
    this.googlePlus
      .login({})
      .then((res) => {
        res.mode = "google";
        console.log(res);
        let data = JSON.stringify(res);
        this.alertCtrl
          .create({
            title: "",
            message: data,
            buttons: [
              {
                text: "OK",
              },
            ],
          })
          .present();
        this.user.socialLogin(res).subscribe(
          (res: any) => {
            console.log(res);
            let data = JSON.stringify(res.details);
            this.alertCtrl
              .create({
                title: "",
                message: data,
                buttons: [
                  {
                    text: "OK",
                  },
                ],
              })
              .present();
            this.loader.dismiss();
            if (res.code == 1) {
              this.user.loggedIn(res.details);
              if (
                this.settings.appSettings.referearn &&
                res.refer_earn == "1"
              ) {
                this.goToReferral();
                this.signupby = "social";
                this.currentUserId = this.user.id;
              } else {
                this.translate
                  .get(["LOGIN_SUCCESS"], {
                    value: res.details.first_name || res.details.username,
                  })
                  .subscribe((x) => {
                    this.toast.show(x.LOGIN_SUCCESS);
                  });
                this.viewCtrl.dismiss({ code: true });
              }
            } else {
              this.translate.get(["LOGIN_FAILED"]).subscribe((x) => {
                this.toast.show(x.LOGIN_FAILED);
              });
            }
          },
          (err) => {
            this.loader.dismiss();
            console.error(err);
            this.toast.show(err.code);
          }
        );
      })
      .catch((err) => {
        console.error(err);
        this.loader.dismiss();
        this.toast.show(err);
      });
  }
  facebookLogin() {
    this.fb
      .login(["public_profile", "email"])
      .then((res: FacebookLoginResponse) => {
        console.log("Logged into Facebook!", res);
        this.fb
          .api("me?fields=id,name,email,first_name,last_name", [])
          .then((res) => {
            console.log(res);
            res.mode = "facebook";
            if (res.email && res.email !== "") {
              this.loader.show();
              this.user.socialLogin(res).subscribe(
                (res: any) => {
                  console.log(res);
                  this.loader.dismiss();
                  if (res.code == 1) {
                    this.user.loggedIn(res.details);
                    if (
                      this.settings.appSettings.referearn &&
                      res.refer_earn == "1"
                    ) {
                      this.goToReferral();
                      this.signupby = "social";
                      this.currentUserId = this.user.id;
                    } else {
                      this.translate
                        .get(["LOGIN_SUCCESS"], {
                          value: res.details.first_name || res.details.username,
                        })
                        .subscribe((x) => {
                          this.toast.show(x.LOGIN_SUCCESS);
                        });
                      this.viewCtrl.dismiss({ code: true });
                    }
                  } else {
                    this.translate.get(["LOGIN_FAILED"]).subscribe((x) => {
                      this.toast.show(x.LOGIN_FAILED);
                    });
                  }
                },
                (err) => {
                  this.loader.dismiss();
                  console.error(err);
                  this.toast.show(err.code);
                }
              );
            } else {
              this.translate
                .get(["FB_EMAIL_REQUIRED"], {
                  value: res.details.first_name || res.details.username,
                })
                .subscribe((x) => {
                  this.toast.show(x.FB_EMAIL_REQUIRED);
                });
            }
          });
      })
      .catch((e) => {
        console.log("Error logging into Facebook", e);
      });
  }
  applyReferralCode() {
    if (!this.referralForm.valid) {
      this.translate.get(["FILL_REQUIRED_FIELD"]).subscribe((x) => {
        this.toast.show(x.FILL_REQUIRED_FIELD);
      });
      return;
    }
    this.loader.show();
    this.user
      .applyReferralCode(this.currentUserId, this.referralForm.value.refer_code)
      .subscribe(
        (x: any) => {
          this.loader.dismiss();
          if (x.status == 1) {
            if (this.signupby === "form") {
              this.translate.get(["REGISTER_SUCCESS"]).subscribe((x) => {
                this.toast.show(x.REGISTER_SUCCESS);
              });
              this.goToLogin();
            } else {
              this.translate
                .get(["LOGIN_SUCCESS"], {
                  value: this.user.first_name || this.user.username,
                })
                .subscribe((x) => {
                  this.toast.show(x.LOGIN_SUCCESS);
                });
              this.viewCtrl.dismiss({ code: true });
            }
          } else {
            this.toast.showWithClose(x.message);
          }
        },
        (err) => {
          this.loader.dismiss();
          console.log(err);
        }
      );
    console.log(this.referralForm.value);
  }
}
