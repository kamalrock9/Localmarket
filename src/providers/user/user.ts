import { Facebook } from '@ionic-native/facebook';
import { GooglePlus } from '@ionic-native/google-plus';
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage';

import { App } from '../../app/app.config';

@Injectable()
export class UserProvider {
  private USER_KEY: string = 'user';

  user: any;
  _readyPromise: Promise<any>;
  url: any;
  constructor(public storage: Storage, private http: HttpClient, private googlePlus: GooglePlus, private fb: Facebook) {
    this.load();
    this.url = App.url + "/wp-json/wc/v2";
  }

  load() {
    return this.storage.get(this.USER_KEY).then((val) => {
      if (val)
        this.loggedIn(val);
    });
  }

  loggedIn(user) {
    this.user = user;
    this.save();
    return this.user;
  }

  login(data: any) {
    let param = {
      email: data.user,
      password: data.pass
    }
    return this.http.post(this.url + '/login', param);
  }
  reset(data: any) {
    let emailField = '?email=' + data.email;
    console.log(emailField);
    return this.http.get(this.url + '/forget-password' + emailField);
  }
  changePassword(data: any) {
    let changefield = '?user_id=' + this.user.id + '&password_current=' + data.opass + '&password_1=' + data.npass + '&password_2=' + data.cpass;
    console.log(changefield);
    return this.http.get(this.url + '/change-password' + changefield);
  }
  socialLogin(data) {
    return this.http.post(this.url + "/social-login", data);
  }
  register(data: any) {
    let fields = 'fname=' + data.fname + '&lname=' + data.lname + '&email=' + data.email + '&password=' + data.pass;
    console.log(fields);
    return this.http.post(this.url + '/register', fields,
      {
        headers: {
          'Content-Type': "application/x-www-form-urlencoded;charset=utf-8"
        }
      });
  }
  getReferEarnData() {
    let u = (this.user && this.user.id) ? "?user_id=" + this.user.id : "?user_id=";
    return this.http.get(this.url + "/refer" + u);
  }
  applyReferralCode(id, code) {
    let fields = "user_id=" + id + "&refer_code=" + code;
    return this.http.post(this.url + "/referapply", fields, {
      headers: {
        'Content-Type': "application/x-www-form-urlencoded;charset=utf-8"
      }
    });
  }

  logout() {
    this.user = null;
    this.googlePlus.disconnect();
    this.fb.logout();
    this.storage.remove(this.USER_KEY).then(() => {
      console.log("Logged Out")
    });
    this.http.get(this.url + '/logout', { responseType: 'text' }).subscribe((data) => {
      console.log(data);
    });
  }

  get email() {
    if (this.user.email)
      return this.user.email;
  }

  get billing() {
    if (this.user && this.user.billing) {
      return this.user.billing;
    } else {
      return null;
    }
  }
  get shipping() {
    if (this.user && this.user.shipping) {
      return this.user.shipping;
    } else {
      return null;
    }
  }

  get first_name() {
    if (this.user.first_name)
      return this.user.first_name;
    else
      return null;
  }

  get last_name() {
    if (this.user.last_name)
      return this.user.last_name;
    else
      return '';
  }

  get id() {
    if (this.user && this.user.id) {
      return this.user.id;
    } else {
      return '';
    }

  }

  get username() {
    if (this.user.username)
      return this.user.username;
  }

  get all() {
    if (this.user)
      return this.user;
  }

  save() {
    return this.storage.set(this.USER_KEY, this.user);
  }

}
