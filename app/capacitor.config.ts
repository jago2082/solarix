import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.systemposfac.parking',
  appName: 'System Pos Fac',
  webDir: 'www',
  server:{
    cleartext:true
  },
  plugins:{
    SplashScreen: {
      launchShowDuration: 3000,
      launchAutoHide: true,
      androidScaleType: "CENTER_CROP",
      splashImmersive: true,
      backgroundColor: "#792f2f"
  },
    CapacitorHttp:{
      enabled:true
    }
  }
};

export default config;
