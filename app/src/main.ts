import {IonicModule} from '@ionic/angular';
import { bootstrapApplication } from '@angular/platform-browser';
import { RouteReuseStrategy, provideRouter, withPreloading, PreloadAllModules } from '@angular/router';
import { IonicRouteStrategy, provideIonicAngular } from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { addOutline, createOutline, trashOutline, closeOutline, people, grid, menu, settings } from 'ionicons/icons';

import { routes } from './app/app.routes';
import { AppComponent } from './app/app.component';
import { provideHttpClient } from '@angular/common/http';
import { importProvidersFrom } from '@angular/core';
import { providePrimeNG } from 'primeng/config';
import Aura from '@primeng/themes/aura';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import localeEsCo from "@angular/common/locales/es-CO";
import { LOCALE_ID } from '@angular/core';
import { DatePipe, registerLocaleData } from '@angular/common';

registerLocaleData(localeEsCo, 'es-CO');

addIcons({
  add: addOutline,
  create: createOutline,
  trash: trashOutline,
  close: closeOutline,
  people,
  grid,
  menu,
  settings
});


bootstrapApplication(AppComponent, {
  providers: [
    { provide: RouteReuseStrategy, useClass: IonicRouteStrategy },DatePipe,
    provideHttpClient(),
    provideIonicAngular(),
    provideRouter(routes, withPreloading(PreloadAllModules)),
    importProvidersFrom(IonicModule.forRoot({})),
    { provide: LOCALE_ID, useValue: 'es-CO' },
    provideAnimationsAsync(),
    providePrimeNG({ 
      theme: {
        preset: Aura
      }
    })
  ],
});
