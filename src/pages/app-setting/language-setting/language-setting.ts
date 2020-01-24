import { SettingsProvider, ToastProvider } from './../../../providers/providers';
import { App } from './../../../app/app.config';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

@IonicPage({
  priority:'low'
})
@Component({
  selector: 'page-language-setting',
  templateUrl: 'language-setting.html',
})
export class LanguageSettingPage {
  form : FormGroup;
  languages: any[] = App.languages;

  constructor(public navCtrl: NavController, public navParams: NavParams,formBuilder:FormBuilder,
     private settings: SettingsProvider,public translate:TranslateService,public toastCtrl:ToastProvider) {
    this.form = formBuilder.group({
      language: [this.settings.all.language || this.translate.currentLang, Validators.required ]
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad LanguageSettingPage');
  }

  submit() {
    this.settings.setSettings(this.form.value.language, 'language');
    this.translate.use(this.form.value.language);

    this.translate.get('LANGUAGE_CHANGED').subscribe( x=> {
      this.toastCtrl.show(x);
    });
  }
}
