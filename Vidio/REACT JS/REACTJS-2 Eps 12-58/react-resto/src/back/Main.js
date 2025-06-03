import React, { useState, useEffect } from "react";
import { Navigate } from "react-router-dom";
import axios from "axios";
import { link } from "../axios/link";
import DashboardCard from "../components/DashboardCard";
import { LineChart, Line, AreaChart, Area, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, RadialBarChart, RadialBar } from "recharts";

export default function Main() {
  const token = sessionStorage.getItem("token");
  const [counts, setCounts] = useState({
    kategori: 0,
    menu: 0,
    pelanggan: 0,
    orders: 0,
  });
  const [loading, setLoading] = useState(true);
  const [chartData, setChartData] = useState({
    categoryDistribution: [],
    dailyOrders: [],
    monthlyTrend: [],
    revenueData: [],
  });

  // Fungsi untuk memproses data kategori
  const processCategoryData = (menuData, kategoriData) => {
    if (!Array.isArray(menuData) || !Array.isArray(kategoriData)) return [];

    const categoryCount = {};
    const colors = ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6", "#F97316", "#06B6D4", "#84CC16"];

    // Hitung jumlah menu per kategori
    menuData.forEach((menu) => {
      const kategoriId = menu.kategori_id || menu.kategori?.id;
      if (kategoriId) {
        const kategori = kategoriData.find((k) => k.id === kategoriId);
        const kategoriName = kategori?.nama || `Kategori ${kategoriId}`;
        categoryCount[kategoriName] = (categoryCount[kategoriName] || 0) + 1;
      }
    });

    return Object.entries(categoryCount).map(([name, value], index) => ({
      name,
      value,
      color: colors[index % colors.length],
    }));
  };

  // Fungsi untuk memproses data order harian (7 hari terakhir)
  const processDailyOrders = (orderData) => {
    if (!Array.isArray(orderData)) return [];

    const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    const last7Days = [];
    const today = new Date();

    // Generate data 7 hari terakhir
    for (let i = 6; i >= 0; i--) {
      const date = new Date(today);
      date.setDate(date.getDate() - i);
      const dayName = days[date.getDay()];

      // Hitung order pada hari tersebut
      const dayOrders = orderData.filter((order) => {
        const orderDate = new Date(order.created_at || order.tanggal);
        return orderDate.toDateString() === date.toDateString();
      });

      // Hitung unique customers
      const uniqueCustomers = new Set(dayOrders.map((order) => order.pelanggan_id)).size;

      last7Days.push({
        day: dayName.slice(0, 3),
        orders: dayOrders.length,
        customers: uniqueCustomers,
        revenue: dayOrders.reduce((sum, order) => sum + (parseFloat(order.total) || 0), 0),
      });
    }

    return last7Days;
  };

  // Fungsi untuk memproses data monthly trend (6 bulan terakhir)
  const processMonthlyTrend = (orderData) => {
    if (!Array.isArray(orderData)) return [];

    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const monthlyData = [];
    const today = new Date();

    for (let i = 5; i >= 0; i--) {
      const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
      const monthName = months[date.getMonth()];

      const monthOrders = orderData.filter((order) => {
        const orderDate = new Date(order.created_at || order.tanggal);
        return orderDate.getMonth() === date.getMonth() && orderDate.getFullYear() === date.getFullYear();
      });

      const revenue = monthOrders.reduce((sum, order) => sum + (parseFloat(order.total) || 0), 0);

      monthlyData.push({
        name: monthName,
        orders: monthOrders.length,
        revenue: revenue,
      });
    }

    return monthlyData;
  };

  // Fungsi untuk memproses data performance
  const processPerformanceData = (counts) => {
    const maxValues = {
      orders: 200,
      menu: 100,
      pelanggan: 500,
      kategori: 50,
    };

    return [
      {
        name: "Orders",
        value: Math.min(100, (counts.orders / maxValues.orders) * 100),
        actualValue: counts.orders,
        fill: "#3B82F6",
      },
      {
        name: "Menu",
        value: Math.min(100, (counts.menu / maxValues.menu) * 100),
        actualValue: counts.menu,
        fill: "#10B981",
      },
      {
        name: "Pelanggan",
        value: Math.min(100, (counts.pelanggan / maxValues.pelanggan) * 100),
        actualValue: counts.pelanggan,
        fill: "#F59E0B",
      },
      {
        name: "Kategori",
        value: Math.min(100, (counts.kategori / maxValues.kategori) * 100),
        actualValue: counts.kategori,
        fill: "#EF4444",
      },
    ];
  };

  useEffect(() => {
    const fetchCounts = async () => {
      try {
        const [kategoriRes, menuRes, pelangganRes, orderRes] = await Promise.all([axios.get(`${link}/kategori`), axios.get(`${link}/menu`), axios.get(`${link}/pelanggan`), axios.get(`${link}/order`)]);

        // Extract data arrays
        const kategoriData = Array.isArray(kategoriRes.data) ? kategoriRes.data : kategoriRes.data?.data || [];
        const menuData = Array.isArray(menuRes.data) ? menuRes.data : menuRes.data?.data || [];
        const pelangganData = Array.isArray(pelangganRes.data) ? pelangganRes.data : pelangganRes.data?.data || [];
        const orderData = Array.isArray(orderRes.data) ? orderRes.data : orderRes.data?.data || [];

        const newCounts = {
          kategori: kategoriData.length,
          menu: menuData.length,
          pelanggan: pelangganData.length,
          orders: orderData.length,
        };

        setCounts(newCounts);

        // Process chart data
        setChartData({
          categoryDistribution: processCategoryData(menuData, kategoriData),
          dailyOrders: processDailyOrders(orderData),
          monthlyTrend: processMonthlyTrend(orderData),
          performanceData: processPerformanceData(newCounts),
        });

        console.log("API Responses:", {
          kategori: kategoriRes.data,
          menu: menuRes.data,
          pelanggan: pelangganRes.data,
          order: orderRes.data,
        });
      } catch (err) {
        console.error("Error fetching dashboard data:", err);
        setCounts({ kategori: 0, menu: 0, pelanggan: 0, orders: 0 });
        setChartData({
          categoryDistribution: [],
          dailyOrders: [],
          monthlyTrend: [],
          performanceData: [],
        });
      } finally {
        setLoading(false);
      }
    };

    fetchCounts();
  }, []);

  if (!token) return <Navigate to="/login" replace />;

  const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(value);
  };

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-600 mt-1">Selamat datang di panel admin RasaNusantara</p>
      </div>

      {/* Cards Section */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <DashboardCard title="Total Kategori" value={loading ? "..." : counts.kategori} icon="📑" color="border-blue-500/50" />
        <DashboardCard title="Total Menu" value={loading ? "..." : counts.menu} icon="🍽️" color="border-green-500/50" />
        <DashboardCard title="Total Pelanggan" value={loading ? "..." : counts.pelanggan} icon="👥" color="border-yellow-500/50" />
        <DashboardCard title="Total Order" value={loading ? "..." : counts.orders} icon="📦" color="border-purple-500/50" />
      </div>

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Monthly Trend Chart */}
        <div className="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-lg font-semibold text-gray-800">📈 Tren Orders & Revenue (6 Bulan)</h3>
            <div className="flex space-x-4 text-sm">
              <div className="flex items-center">
                <div className="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                <span className="text-gray-600">Orders</span>
              </div>
              <div className="flex items-center">
                <div className="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span className="text-gray-600">Revenue</span>
              </div>
            </div>
          </div>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={chartData.monthlyTrend}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="name" stroke="#6b7280" />
              <YAxis yAxisId="left" stroke="#6b7280" />
              <YAxis yAxisId="right" orientation="right" stroke="#6b7280" />
              <Tooltip
                contentStyle={{
                  backgroundColor: "#fff",
                  border: "1px solid #e5e7eb",
                  borderRadius: "8px",
                  boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                }}
                formatter={(value, name) => [name === "revenue" ? formatCurrency(value) : value, name === "orders" ? "Orders" : "Revenue"]}
              />
              <Line yAxisId="left" type="monotone" dataKey="orders" stroke="#3B82F6" strokeWidth={3} dot={{ fill: "#3B82F6", strokeWidth: 2, r: 4 }} activeDot={{ r: 6, stroke: "#3B82F6", strokeWidth: 2, fill: "#fff" }} />
              <Line yAxisId="right" type="monotone" dataKey="revenue" stroke="#10B981" strokeWidth={3} dot={{ fill: "#10B981", strokeWidth: 2, r: 4 }} activeDot={{ r: 6, stroke: "#10B981", strokeWidth: 2, fill: "#fff" }} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Daily Orders Chart */}
        <div className="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
          <h3 className="text-lg font-semibold text-gray-800 mb-6">📊 Orders & Pelanggan (7 Hari Terakhir)</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={chartData.dailyOrders}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="day" stroke="#6b7280" />
              <YAxis stroke="#6b7280" />
              <Tooltip
                contentStyle={{
                  backgroundColor: "#fff",
                  border: "1px solid #e5e7eb",
                  borderRadius: "8px",
                }}
              />
              <Legend />
              <Bar dataKey="orders" fill="#3B82F6" name="Orders" radius={[4, 4, 0, 0]} />
              <Bar dataKey="customers" fill="#10B981" name="Pelanggan" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>

        {/* Performance Radial Chart */}
        <div className="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
          <h3 className="text-lg font-semibold text-gray-800 mb-6">⚡ Performance Overview</h3>
          <ResponsiveContainer width="100%" height={300}>
            <RadialBarChart cx="50%" cy="50%" innerRadius="20%" outerRadius="90%" data={chartData.performanceData}>
              <RadialBar minAngle={15} label={{ position: "insideStart", fill: "#fff", fontSize: 12 }} background clockWise dataKey="value" />
              <Legend iconSize={10} layout="horizontal" verticalAlign="bottom" align="center" formatter={(value, entry) => `${entry.payload.name}: ${entry.payload.actualValue}`} />
              <Tooltip
                formatter={(value, name, props) => [`${props.payload.actualValue}`, props.payload.name]}
                contentStyle={{
                  backgroundColor: "#fff",
                  border: "1px solid #e5e7eb",
                  borderRadius: "8px",
                }}
              />
            </RadialBarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Revenue Summary */}
      {chartData.dailyOrders.length > 0 && (
        <div className="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
          <h3 className="text-lg font-semibold text-gray-800 mb-6">💰 Revenue Summary (7 Hari Terakhir)</h3>
          <ResponsiveContainer width="100%" height={250}>
            <AreaChart data={chartData.dailyOrders}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="day" stroke="#6b7280" />
              <YAxis stroke="#6b7280" />
              <Tooltip
                formatter={(value) => [formatCurrency(value), "Revenue"]}
                contentStyle={{
                  backgroundColor: "#fff",
                  border: "1px solid #e5e7eb",
                  borderRadius: "8px",
                }}
              />
              <Area type="monotone" dataKey="revenue" stroke="#8B5CF6" fill="url(#revenueGradient)" strokeWidth={2} />
              <defs>
                <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#8B5CF6" stopOpacity={0.3} />
                  <stop offset="95%" stopColor="#8B5CF6" stopOpacity={0.1} />
                </linearGradient>
              </defs>
            </AreaChart>
          </ResponsiveContainer>
        </div>
      )}
    </div>
  );
}
