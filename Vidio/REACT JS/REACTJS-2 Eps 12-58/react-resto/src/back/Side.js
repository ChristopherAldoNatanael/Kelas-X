import React, { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";

const Side = () => {
  const [isExpanded, setIsExpanded] = useState(true);
  const location = useLocation();
  const navigate = useNavigate();
  const level = sessionStorage.getItem("level");

  const handleLogout = () => {
    // Clear all session data
    sessionStorage.clear();
    // Navigate to login page
    navigate("/login");
  };

  const menuItems = [
    {
      title: "Main",
      items: [
        {
          to: "/admin",
          label: "Dashboard",
          icon: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
          access: ["admin", "kasir", "koki"],
        },
      ],
    },
    {
      title: "Management",
      items: [
        {
          to: "/admin/kategori",
          label: "Kategori",
          icon: "M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5c.78.78.78 2.047 0 2.828l-5 5a2 2 0 01-2.828 0l-5-5a2 2 0 010-2.828l5-5A2 2 0 017 3z",
          access: ["admin"],
        },
        {
          to: "/admin/menu",
          label: "Menu",
          icon: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
          access: ["admin"],
        },
        {
          to: "/admin/pelanggan",
          label: "Pelanggan",
          icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
          access: ["admin"],
        },
        {
          to: "/admin/user",
          label: "Users",
          icon: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
          access: ["admin"],
        },
      ],
    },
    {
      title: "Orders",
      items: [
        {
          to: "/admin/order",
          label: "Order List",
          icon: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01",
          access: ["admin", "kasir"],
        },
        {
          to: "/admin/order-detail",
          label: "Order Details",
          icon: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
          access: ["admin", "kasir", "koki"],
        },
      ],
    },
  ];

  return (
    <div className={`h-screen bg-white border-r border-gray-200 transition-all duration-300 ${isExpanded ? "w-64" : "w-20"}`}>
      <div className="flex flex-col h-full">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b border-gray-200">
          {isExpanded && (
            <div className="flex items-center space-x-3">
              <div className="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                <span className="text-white text-xl font-bold">R</span>
              </div>
              <span className="font-bold text-gray-800">RasaNusantara</span>
            </div>
          )}
          <button onClick={() => setIsExpanded(!isExpanded)} className="p-2 hover:bg-gray-100 rounded-lg">
            <svg className={`w-5 h-5 text-gray-500 transition-transform duration-300 ${isExpanded ? "rotate-180" : ""}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto py-4">
          {menuItems.map((section, idx) => (
            <div key={idx} className="px-4 mb-6">
              {isExpanded && <h3 className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{section.title}</h3>}
              <div className="space-y-1">
                {section.items
                  .filter((item) => item.access.includes(level))
                  .map((item, index) => (
                    <Link key={index} to={item.to} className={`flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-200 ${location.pathname === item.to ? "bg-blue-50 text-blue-600" : "text-gray-600 hover:bg-gray-50"}`}>
                      <svg className={`w-5 h-5 ${location.pathname === item.to ? "text-blue-600" : "text-gray-400"}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={item.icon} />
                      </svg>
                      {isExpanded && <span className="ml-3">{item.label}</span>}
                    </Link>
                  ))}
              </div>
            </div>
          ))}
        </nav>

        {/* User Profile */}
        <div className="p-4 border-t border-gray-200">
          <div className="flex items-center space-x-3 mb-4">
            <div className="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center">
              <span className="text-white font-bold">{sessionStorage.getItem("email")?.charAt(0).toUpperCase()}</span>
            </div>
            {isExpanded && (
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-900 truncate">{sessionStorage.getItem("name") || sessionStorage.getItem("email")}</p>
                <p className="text-xs text-gray-500 capitalize">{sessionStorage.getItem("level")}</p>
              </div>
            )}
          </div>

          {/* Logout Button */}
          <button onClick={handleLogout} className={`w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 ${!isExpanded && "px-2"}`}>
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {isExpanded && <span>Logout</span>}
          </button>
        </div>
      </div>
    </div>
  );
};

export default Side;
