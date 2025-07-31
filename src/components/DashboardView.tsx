import React from 'react';
import { Car, MapPin, Users, DollarSign, AlertTriangle, CheckCircle } from 'lucide-react';
import StatCard from './StatCard';

const DashboardView: React.FC = () => {
  const recentTrips = [
    { id: 1, route: 'Lubumbashi - Kolwezi', driver: 'Jean Mukamba', status: 'En cours', departure: '08:30' },
    { id: 2, route: 'Lubumbashi - Likasi', driver: 'Marie Kabila', status: 'Terminé', departure: '07:00' },
    { id: 3, route: 'Lubumbashi - Kipushi', driver: 'Paul Tshisekedi', status: 'En attente', departure: '10:00' },
    { id: 4, route: 'Lubumbashi - Fungurume', driver: 'Grace Mbuyi', status: 'En cours', departure: '09:15' },
  ];

  const vehicleAlerts = [
    { id: 1, vehicle: 'Bus 001 - Toyota Hiace', issue: 'Maintenance programmée', severity: 'warning' },
    { id: 2, vehicle: 'Bus 003 - Nissan Civilian', issue: 'Révision technique expirée', severity: 'danger' },
    { id: 3, vehicle: 'Bus 007 - Mercedes Sprinter', issue: 'Entretien nécessaire', severity: 'info' },
  ];

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'En cours': return 'bg-blue-100 text-blue-800';
      case 'Terminé': return 'bg-green-100 text-green-800';
      case 'En attente': return 'bg-yellow-100 text-yellow-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getAlertColor = (severity: string) => {
    switch (severity) {
      case 'danger': return 'bg-red-100 text-red-800 border-red-200';
      case 'warning': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      case 'info': return 'bg-blue-100 text-blue-800 border-blue-200';
      default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Total Véhicules"
          value={12}
          change="+2 ce mois"
          changeType="positive"
          icon={Car}
          color="blue"
        />
        <StatCard
          title="Trajets Aujourd'hui"
          value={8}
          change="+15% vs hier"
          changeType="positive"
          icon={MapPin}
          color="green"
        />
        <StatCard
          title="Chauffeurs Actifs"
          value={10}
          change="2 en congé"
          changeType="neutral"
          icon={Users}
          color="orange"
        />
        <StatCard
          title="Revenus du Jour"
          value="$2,450"
          change="+8.5% vs hier"
          changeType="positive"
          icon={DollarSign}
          color="green"
        />
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Trips */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200">
          <div className="p-6 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">Trajets Récents</h3>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {recentTrips.map((trip) => (
                <div key={trip.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div className="flex-1">
                    <p className="font-medium text-gray-900">{trip.route}</p>
                    <p className="text-sm text-gray-600">Chauffeur: {trip.driver}</p>
                    <p className="text-sm text-gray-500">Départ: {trip.departure}</p>
                  </div>
                  <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(trip.status)}`}>
                    {trip.status}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Vehicle Alerts */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200">
          <div className="p-6 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">Alertes Véhicules</h3>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {vehicleAlerts.map((alert) => (
                <div key={alert.id} className={`p-4 rounded-lg border ${getAlertColor(alert.severity)}`}>
                  <div className="flex items-start space-x-3">
                    {alert.severity === 'danger' && <AlertTriangle size={20} className="text-red-600 mt-0.5" />}
                    {alert.severity === 'warning' && <AlertTriangle size={20} className="text-yellow-600 mt-0.5" />}
                    {alert.severity === 'info' && <CheckCircle size={20} className="text-blue-600 mt-0.5" />}
                    <div className="flex-1">
                      <p className="font-medium">{alert.vehicle}</p>
                      <p className="text-sm opacity-80">{alert.issue}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Routes Overview */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200">
        <div className="p-6 border-b border-gray-200">
          <h3 className="text-lg font-semibold text-gray-900">Aperçu des Routes Principales</h3>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
              <h4 className="font-semibold text-blue-900 mb-2">Lubumbashi - Kolwezi</h4>
              <p className="text-2xl font-bold text-blue-700">3 trajets/jour</p>
              <p className="text-sm text-blue-600">Distance: 250km</p>
            </div>
            <div className="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
              <h4 className="font-semibold text-green-900 mb-2">Lubumbashi - Likasi</h4>
              <p className="text-2xl font-bold text-green-700">4 trajets/jour</p>
              <p className="text-sm text-green-600">Distance: 120km</p>
            </div>
            <div className="text-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
              <h4 className="font-semibold text-orange-900 mb-2">Lubumbashi - Kipushi</h4>
              <p className="text-2xl font-bold text-orange-700">2 trajets/jour</p>
              <p className="text-sm text-orange-600">Distance: 75km</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardView;