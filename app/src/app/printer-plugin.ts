// src/app/printer-plugin.ts

export interface PrintTextOptions {
  text: string;
}

export interface PrinterPluginImpl {
  printText(options: PrintTextOptions): Promise<void>;
  printSalida(options: { data: any }): Promise<void>;
  printEntrada(options: { data: any }): Promise<void>;
}

export const PrinterPlugin: PrinterPluginImpl = {
  printText: async () => {},
  printSalida: async () => {},
  printEntrada: async () => {},
};

  