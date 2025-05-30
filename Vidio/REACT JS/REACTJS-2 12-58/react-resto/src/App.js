import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import ErrorBoundary from "./components/ErrorBoundary";
import Back from "./back/Back";
import Front from "./front/front";
import OrderDetail from "./back/OrderDetail";
import Login from "./back/Login";
import Home from "./front/Home"; // halaman utama pelanggan
import MenuComponent from "./front/MenuComponent";
import CartComponent from "./front/CartComponent";
import SettingComponent from "./front/SettingComponent";
import HistoryComponent from "./front/HistoryComponent";
import RegisterComponent from "./front/RegisterComponent";
import LoginCustomer from "./front/LoginCustomer";

function App() {
  return (
    <BrowserRouter>
      <ErrorBoundary>
        <Routes>
          <Route path="/admin/*" element={<Back />} />
          <Route path="/*" element={<Front />} />
          <Route path="/admin/order-detail" element={<OrderDetail />} />
          <Route path="/login" element={<Login />} />
          <Route path="/" element={<Home />} />
          <Route path="/menu" element={<MenuComponent />} />
          <Route path="/cart" element={<CartComponent />} />
          <Route path="/profile/setting" element={<SettingComponent />} />
          <Route path="/profile/history" element={<HistoryComponent />} />
          <Route path="/register" element={<RegisterComponent />} />
          <Route path="/login-customer" element={<LoginCustomer />} />
        </Routes>
      </ErrorBoundary>
    </BrowserRouter>
  );
}

export default App;
