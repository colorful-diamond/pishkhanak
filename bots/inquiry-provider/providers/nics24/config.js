/**
 * NICS24 Provider Configuration
 * Alternative to credentials.json since it may be blocked
 */

export const nics24Config = {
  users: [
    {
      username: "pishkhanak",
      password: "@Alislami67",
      description: "NICS24 primary user account - UPDATE WITH REAL CREDENTIALS"
    }
  ],
  provider: "nics24",
  baseUrl: "https://etebarito.nics24.ir",
  endpoints: {
    login: "/login-username",
    loginWithNationalCode: "/login",
    sendOtp: "/api/ShareReportWithOtp/SendOtp-payment",
    getCaptcha: "/api/Captcha/GetCaptcha",
    verifyOtp: "/api/ShareReportWithOtp",
    dashboard: "/pishkhan"
  },
  captchaApiUrl: "http://localhost:9090/predict",
  sessionConfig: {
    maxAge: 2 * 60 * 60 * 1000, // 2 hours in milliseconds
    refreshBeforeExpiry: 30 * 60 * 1000, // Refresh 30 minutes before expiry
    checkInterval: 10 * 60 * 1000 // Check every 10 minutes
  },
  description: "NICS24 Credit Score Provider Configuration"
};

export default nics24Config;