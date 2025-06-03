import React from "react";

const DashboardCard = ({ title, value, icon, color }) => {
  return (
    <div className={`bg-white rounded-2xl shadow-lg p-6 border-l-4 ${color}`}>
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-gray-600">{title}</p>
          <p className="text-3xl font-bold text-gray-900 mt-1">{value}</p>
        </div>
        <div className={`w-12 h-12 rounded-full flex items-center justify-center ${color.replace("border-", "bg-").replace("/50", "/10")}`}>
          <span className="text-2xl">{icon}</span>
        </div>
      </div>
    </div>
  );
};

export default DashboardCard;
