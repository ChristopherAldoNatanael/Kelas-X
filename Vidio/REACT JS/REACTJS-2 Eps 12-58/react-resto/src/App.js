import React from "react";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import ErrorBoundary from "./components/ErrorBoundary";
import ProtectedRoute from "./components/ProtectedRoute";
import CustomerProtectedRoute from "./components/CustomerProtectedRoute";
import Back from "./back/Back";
import Front from "./front/front";
import OrderDetail from "./back/OrderDetail";
import Login from "./back/Login";
import Home from "./front/Home";
import MenuComponent from "./front/MenuComponent";
import CartComponent from "./front/Cart";
import SettingComponent from "./front/SettingComponent";
import HistoryComponent from "./front/HistoryComponent";
import RegisterComponent from "./front/RegisterComponent";
import LoginCustomer from "./front/LoginCustomer";
import CheckoutComponent from "./front/CheckoutComponent";

function App() {
  return (
    <BrowserRouter>
      <ErrorBoundary>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route
            path="/admin/*"
            element={
              <ProtectedRoute>
                <Back />
              </ProtectedRoute>
            }
          />
          {/* Pindahkan rute Front ke bawah untuk menghindari konflik */}
          <Route path="/" element={<Home />} />
          <Route path="/menu" element={<MenuComponent />} />
          <Route path="/cart" element={<CartComponent />} />
          <Route
            path="/profile/setting"
            element={
              <CustomerProtectedRoute>
                <SettingComponent />
              </CustomerProtectedRoute>
            }
          />
          <Route path="/history" element={<HistoryComponent />} />
          <Route path="/register" element={<RegisterComponent />} />
          <Route path="/login-customer" element={<LoginCustomer />} />
          <Route path="/checkout" element={<CheckoutComponent />} />
          <Route path="/admin/order-detail" element={<OrderDetail />} />
          <Route path="/*" element={<Front />} /> {/* Pindahkan ke bawah */}
        </Routes>
      </ErrorBoundary>
    </BrowserRouter>
  );
}

export default App;
