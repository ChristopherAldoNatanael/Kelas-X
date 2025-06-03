import React from "react";
import { Route, Routes } from "react-router-dom";
import Main from "./Main";
import Kategori from "./kategori";
import Menu from "./menu";
import Pelanggan from "./pelanggan";
import Order from "./order";
import OrderDetail from "./OrderDetail";
import User from "./User"; // Add User import

export default function Content() {
  return (
    <div>
      <Routes>
        <Route path="/" element={<Main />} />
        <Route path="/kategori" element={<Kategori />} />
        <Route path="/menu" element={<Menu />} />
        <Route path="/order" element={<Order />} />
        <Route path="/order-detail" element={<OrderDetail />} />
        <Route path="/pelanggan" element={<Pelanggan />} />
        <Route path="/user" element={<User />} /> {/* Replace h2 with User component */}
      </Routes>
    </div>
  );
}
