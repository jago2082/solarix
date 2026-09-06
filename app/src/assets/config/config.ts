import { gnconex } from "../../app/models/gnconex";

// Configuración general de la aplicación
export const centralizacionUrl: string = "https://consultoria.digitalware.co:442/Apps/Centralizacion/api/";
export const appVersion: string = '26.0.8.0';
export const appCopyright: string = 'SYSTEM POSFAC © 2020-2025';

// Tiendas de aplicaciones
export const appGooglePlayUrl: string = "";
export const appAppStoreUrl: string = "";

// Información del desarrollador
export const developer: string = 'ESP SOLUCIONES INFORMATICAS S.A.S.';
export const developerMail: string = 'proyectos@systemposfac.com.co';
export const developerWeb: string = 'https://www.systemposfac.com.co';
export const developerMode: boolean = false;

// URLs y cliente
export const developerUrl: string = "https://api.enersolax.systemposfac.com.co/";
export const url_cliente: string = "https://api.enersolax.systemposfac.com.co/";
export const name_client: string = "SOLAXGEN";

// Recursos
export const logoCompany: string = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKoAAAA5CAYAAACmhLBvAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAhdEVYdENyZWF0aW9uIFRpbWUAMjAyNjowODoyNSAxOTozMDo1OL/2oWMAAA/wSURBVHhe7ZsJcBVVFoZPEkAICVsSlqAMiwriyqgoWiAooIiMQZChFBhxHLFUUEpQqVEoR7RGXMZ1dCo4og6yKQXIZhAUEYQAWUhCCAkhIRBCQhYSsr/kzvlv3+Z1vyV5DwLlq7pf1an0Xbr7dfff595z+iZIMKTR/M4JVn81mt81WqiagEALVRMQaKFqAgItVE1AoIWqCQi0UDUBgRaqJiDQQtUEBFqomoBAC1UTEGihagICLVRNQHBRhVrb6KB15emqpNGcPxdlmR8EurQihd4vS6LLRDDt7f0X1RJ4/Pzzz1RUVKRKRJdffjkNGTJElTSXihYXal79GfrTydWU56gmYpH2CQ6jvX2mqNbAY/jw4bR9+3ZVInr44Ydp5cqVqqS5VJzX0L+9Jou9Zr0q2Vl8JpnFyiJt5EObptFcIH6rqFE00pyyLTS26Bs6Vl+map0cqOM6EWJYYyvjr0Zzgfgt1LT6U5TtOEuJtadpVMFyN7E2Si9qESm2LVQ21NPMnHgamhZHf8veS2uLj1NNg0O1ajSe8VuoO2uOG+JjEZ5uqKO/Fv4gg6dziCAlUEOwrYkFqzjrqKNJR3bQ8rJ8OlRbQ2tKC+mJoyk0KPkXyqisUL00Gnf8FmpBQxULkXdjC2JLrCmh6Sc3yymBxPSi+MvtQ0K7y2qI9M/ZOym+stzWDkFP6NKD+rcPl/00Gk/4HfV/U5FKs4p/MjymEizsm+6jaHR4b3owN452VhbJ9rFh0fTJFUMohN+HiVk7KL6KRWruI8UaTCPCI2hp/0HUOpjrfMThcNC3335LmzZtosLCQgoJCaGbbrqJJk+eTNddd53q5Q4uddeuXbR161bat28f1dfXU+fOnWno0KE0adIkioiIUD2d+BL14zjff/89/fjjj3T06FEKCgqiG2+8kR599FH5e/Ly8mj37t2qN8m2K664gtavX69qDHANV111lSo52bx5M1VUOEeca665xu06Dxw4QBs3bqTffvuN6urqqEePHnTvvfdSTEyMbF+3bp38C8LDw+m+++5TJSfFxcX03Xff0Z49eyg/P1/e1wEDBsjj3HPPPRTs8oxwrbiPJrfccotM323YsIHi4uJke+vWreU9fOSRR6h7d8NpnRcQqj9wtC9mnYoTEVn/FhGZn4mIjFgRcehzEVucItsfy90u/pG/X2RUlcpylaNeTM78VUQmrBGR+78XkXs3iMj4TSJyT5z4e3a6qGtokP18ZdWqVeLKK6/Ey+VmrVq1ElOmTBF8k1VvJ4mJiYIF6XE/WJs2beS+x48fV3sY3HwwwfT3vXrr11llU1sdr2X169fX4124e/fuo1atX7/e+p4/s2a1a1ev1q9e3T1/v48cOnSY1at3X3U/01q3bt3F3Llzdd3evXs/sGbN/ftt3LixWLNmTfXj/fv3i/Pnz4u0tDRx9uxZ3a/u81//+tf/ePbsWZGTk3Nfd+3atap94cKF4u233xa/vff/A3v//v3i6NGj9+/fv1/ceeed6sfp6enize39/64A///zH2oE+/z586pP2X///X133XWX2LBhg3jvvffUfufOnf2f/3v21yL183x8fHzqxx06dNDjffLJJ2Ljxo3yvO/y4/R96KGHxL333iueeeYZ8fbbb4vCwsL/c+/e3f3/3n306FHx1ltviZkzZ4qPPvpI9/v3v/+tnn///v3iwIED+56y1/Xee++Jn3/+WT33ypUrxcsvvyw+//xzcezYMf3ePffcI5KSkoQpS0pKW3XatGlSly4t1p3Xf/Xq1fL7/fv3ixdeeEGkpKToXnPnzpX6s/fI9N8//vhjMW/ePP3b/1i0aJHYsmWLiI2NFXv27Hnr3///gXv37u3+m7/H292d3v3I3z937tz/3P+ff/4pFi5cKJycnNS5/ePbb78VaWlpYt26dVKmefPm3f/4/3/6/f1//vx523EGBQUJWwX25JNPqvbBgwdrf159340bN+rv3aRJk8Xzzz8vtm/fLtXGjh37wAMP/L+/8s4433777f98993/G9j/4x24f3p6utQ/efJkvV84X2xsrPox23///fe/1atXqwP7qaeekvM1a9ZMv/fcc88JHvOee/14bNmypa2v3/e6+2A/q5977jn1fvA+/fjjj+pve/vtt+v19f/8eSNGjJA0N23aJOnn92v3v//v3bv//v/9+2/3X6m/R/yL/2f+/fv4v5Xp0/3d/8//3/+/2///77v+/r+/839+7eXl5frfH/38f/8+f1aI1f5v+c63W3e93X3m+Xfv+c712Xv+4m/63r//4/8+/718p0412+3+b/v3fv/312v1m/79f3/f/f///p360a7/1//3v2///423f/s3f32e92e//27aO+/58v3v93f1++e163f97u6/+u7d/f+b7d/9s73/f3X5ffvvf//916p1a///132+/+//0+e2//33m3f+v///8+/f+xvv7/233X9/mO8/2O39d3/3f+3/+//+//9b";

// Configuración de conexión principal
export const settings: gnconex = {
  id: 2,
  CNX_NOMB: name_client,
  CNX_IPSR: url_cliente,
  CNX_BACK: "#008E45",
  CNX_LOGO: logoCompany,
  CNX_LINK: "",
  CNX_CPRI: "",
  CNX_CSEG: "",
  CNX_CTER: "",
  CNX_FCLA: "S"
};