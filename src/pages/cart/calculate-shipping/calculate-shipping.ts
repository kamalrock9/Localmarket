import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SettingsProvider } from './../../../providers/providers';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ViewController, Platform } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

@IonicPage({
  priority:'low'
})
@Component({
  selector: 'page-calculate-shipping',
  templateUrl: 'calculate-shipping.html',
})
export class CalculateShippingPage {
  countries: Array<any> = [];
  states: Array<any> = [];
  countryOpts: any = {};
  stateOpts: any = {};
  form: FormGroup;
  data: any = { "country": "", "state": "", "postcode": "" };
dir:string;
  constructor(public navCtrl: NavController, public navParams: NavParams, private formBuilder: FormBuilder,platform:Platform,
    private viewCtrl: ViewController, private translate: TranslateService, public settings: SettingsProvider) {
      this.dir=platform.dir();

    this.translate.get(['SELECT_COUNTRY', 'SELCT_COUNTRY_DESC', 'SELECT_STATE', 'SELCT_STATE_DESC']).subscribe((x) => {
      this.countryOpts = {
        //title: x.SELECT_COUNTRY,
        subTitle: x.SELCT_COUNTRY_DESC
      };
      this.stateOpts = {
        // title: x.SELECT_STATE,
        subTitle: x.SELCT_STATE_DESC
      }
    });
    this.data = navParams.data.params;

    this.form = this.formBuilder.group({
      country: [this.data.country, Validators.required],
      state: [this.data.state],
      postcode: [this.data.postcode]
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad CalculateShippingPage');
    this.countries = this.settings.countryList;
    this.getStates();
  }
  close() {
    this.viewCtrl.dismiss(null);
  }
  submit() {
    this.viewCtrl.dismiss(this.form.value);
  }

  getStates() {
    let id = this.form.get('country').value || this.data.state;
    this.states = this.settings.getState(id);
    if (this.states.length == 0) {
      this.form.controls['state'].setValue('');
    }
  }
}
