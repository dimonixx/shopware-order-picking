import OrderPickingPlugin from "./order-picking-plugin/order-picking.plugin";

const PluginManager = window.PluginManager;
PluginManager.register('OrderPickingPlugin', OrderPickingPlugin, '[data-order-picking-plugin]');